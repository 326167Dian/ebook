<?php

namespace App\Http\Controllers;

use App\Models\EbookContent;
use App\Models\Member;
use App\Models\Reseller;
use App\Models\MidtransPayment;
use App\Services\MidtransService;
use App\Rules\ValidReferralCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class MidtransPaymentController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans)
    {
    }

    public function createForRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:members,email'],
            'referral_code' => ['nullable', 'string', 'max:20', new ValidReferralCode()],
        ]);

        $data['referral_code'] = Reseller::idFromReferralCode($data['referral_code'] ?? null);

        $amount = EbookContent::priceFor($data['referral_code']);

        $payment = $this->createMidtransPayment('register', $amount, [
            'pending_name' => $data['name'],
            'pending_email' => $data['email'],
            'id_reseller' => $data['referral_code'],
        ]);

        if (! $payment) {
            return back()->withInput()->withErrors([
                'referral_code' => 'Pembayaran Midtrans sedang tidak tersedia. Silakan gunakan upload bukti transfer manual.',
            ]);
        }

        return redirect()->route('member.midtrans.show', $payment->order_id);
    }

    public function createForPayment(Request $request)
    {
        $member = $this->resolveSessionMember($request);

        if (! $member) {
            return redirect()->route('member.login');
        }

        if ($member->is_active) {
            return redirect()->route('ebook.home');
        }

        $data = $request->validate([
            'referral_code' => ['nullable', 'string', 'max:20', new ValidReferralCode()],
        ]);

        $data['referral_code'] = Reseller::idFromReferralCode($data['referral_code'] ?? null);

        $amount = EbookContent::priceFor($data['referral_code']);

        $payment = $this->createMidtransPayment('payment', $amount, [
            'member_id' => $member->id,
            'id_reseller' => $data['referral_code'],
        ]);

        if (! $payment) {
            return back()->withErrors([
                'referral_code' => 'Pembayaran Midtrans sedang tidak tersedia. Silakan gunakan upload bukti transfer manual.',
            ]);
        }

        return redirect()->route('member.midtrans.show', $payment->order_id);
    }

    public function show(Request $request, string $orderId)
    {
        $payment = MidtransPayment::query()->where('order_id', $orderId)->firstOrFail();

        $this->refreshExpiry($payment);

        if ($payment->status === 'paid') {
            return redirect($this->successRedirectUrl($payment))->with('status', 'Pembayaran sudah diterima. Terima kasih!');
        }

        return view('member.midtrans-show', [
            'payment' => $payment,
            'cancelUrl' => $payment->flow === 'register' ? route('member.register') : route('member.payment'),
        ]);
    }

    public function status(Request $request, string $orderId)
    {
        $payment = MidtransPayment::query()->where('order_id', $orderId)->firstOrFail();

        $this->refreshExpiry($payment);

        if ($payment->status === 'pending' && (! $payment->last_checked_at || $payment->last_checked_at->diffInSeconds(now()) >= 3)) {
            $this->pollMidtransStatus($payment);

            if ($payment->status === 'paid') {
                $request->session()->forget('referral_id_reseller');
            }
        }

        return response()->json([
            'status' => $payment->status,
            'redirect' => $payment->status === 'paid' ? $this->successRedirectUrl($payment) : null,
        ]);
    }

    private function createMidtransPayment(string $flow, int $amount, array $attributes): ?MidtransPayment
    {
        $orderId = 'ebook-' . $flow . '-' . now()->format('YmdHis') . '-' . Str::random(8);
        $expiryMinutes = (int) config('midtrans.qr_expiry_minutes');

        $payment = MidtransPayment::create(array_merge([
            'flow' => $flow,
            'order_id' => $orderId,
            'amount' => $amount,
            'status' => 'pending',
            'expires_at' => now()->addMinutes($expiryMinutes),
        ], $attributes));

        try {
            $result = $this->midtrans->createQrisCharge($orderId, $amount, $expiryMinutes);
        } catch (Throwable $exception) {
            report($exception);
            $payment->delete();

            return null;
        }

        if (empty($result['qr_string']) && empty($result['qr_image_url'])) {
            $payment->delete();

            return null;
        }

        $payment->forceFill([
            'midtrans_transaction_id' => $result['transaction_id'] ?? null,
            'qr_string' => $result['qr_string'] ?? null,
            'qr_image_url' => $result['qr_image_url'] ?? null,
        ])->save();

        return $payment;
    }

    private function pollMidtransStatus(MidtransPayment $payment): void
    {
        try {
            $result = $this->midtrans->getStatus($payment->order_id);
        } catch (Throwable $exception) {
            report($exception);
            $payment->forceFill(['last_checked_at' => now()])->save();

            return;
        }

        $payment->last_checked_at = now();

        $transactionStatus = (string) ($result['transaction_status'] ?? '');

        if (MidtransService::isPaidStatus($transactionStatus)) {
            if (! $payment->receivedAmountMatches($result['gross_amount'] ?? null)) {
                Log::warning('midtrans.amount_mismatch', [
                    'midtrans_payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'expected_amount' => $payment->amount,
                    'received_amount' => $result['gross_amount'] ?? null,
                ]);
                $payment->update(['status' => 'failed']);

                return;
            }

            $payment->markAsPaid();

            return;
        }

        if (MidtransService::isFailedStatus($transactionStatus)) {
            $payment->status = 'failed';
        }

        $payment->save();
    }

    private function refreshExpiry(MidtransPayment $payment): void
    {
        if ($payment->status === 'pending' && $payment->expires_at->isPast()) {
            $payment->update(['status' => 'expired']);
        }
    }

    private function successRedirectUrl(MidtransPayment $payment): string
    {
        return $payment->flow === 'register'
            ? route('member.login')
            : route('ebook.home');
    }

    private function resolveSessionMember(Request $request): ?Member
    {
        $memberId = (int) $request->session()->get('member_id', 0);

        if ($memberId <= 0) {
            return null;
        }

        return Member::query()->find($memberId);
    }
}
