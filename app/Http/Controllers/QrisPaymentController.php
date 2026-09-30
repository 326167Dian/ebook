<?php

namespace App\Http\Controllers;

use App\Models\EbookContent;
use App\Models\Member;
use App\Models\QrisPayment;
use App\Services\GdcPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class QrisPaymentController extends Controller
{
    public function __construct(private readonly GdcPayService $gdcPay)
    {
    }

    public function createForRegistration(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:150', 'unique:members,email'],
            'referral_code' => [
                'required',
                'integer',
                Rule::exists('reseller', 'id_reseller')->where(fn ($query) => $query->where('is_active', true)),
            ],
        ], [
            'referral_code.required' => 'Kode referral wajib diisi.',
            'referral_code.integer' => 'Kode referral tidak valid, silakan masukkan kode yang benar.',
            'referral_code.exists' => 'Kode referral tidak ditemukan, silakan masukkan kode yang benar.',
        ]);

        $amount = EbookContent::query()->firstOrCreate([], EbookContent::defaultData())->payment_price_final;

        $qrisPayment = $this->createQrisPayment('register', $amount, [
            'pending_name' => $data['name'],
            'pending_email' => $data['email'],
            'id_reseller' => $data['referral_code'],
        ]);

        if (! $qrisPayment) {
            return back()->withInput()->withErrors([
                'referral_code' => 'Pembayaran QRIS sedang tidak tersedia. Silakan gunakan upload bukti transfer manual.',
            ]);
        }

        return redirect()->route('member.qris.show', $qrisPayment->reference_no);
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
            'referral_code' => [
                'required',
                'integer',
                Rule::exists('reseller', 'id_reseller')->where(fn ($query) => $query->where('is_active', true)),
            ],
        ], [
            'referral_code.required' => 'Kode referral wajib diisi.',
            'referral_code.integer' => 'Kode referral tidak valid, silakan masukkan kode yang benar.',
            'referral_code.exists' => 'Kode referral tidak ditemukan, silakan masukkan kode yang benar.',
        ]);

        $amount = EbookContent::query()->firstOrCreate([], EbookContent::defaultData())->payment_price_final;

        $qrisPayment = $this->createQrisPayment('payment', $amount, [
            'member_id' => $member->id,
            'id_reseller' => $data['referral_code'],
        ]);

        if (! $qrisPayment) {
            return back()->withErrors([
                'referral_code' => 'Pembayaran QRIS sedang tidak tersedia. Silakan gunakan upload bukti transfer manual.',
            ]);
        }

        return redirect()->route('member.qris.show', $qrisPayment->reference_no);
    }

    public function show(Request $request, string $reference)
    {
        $qrisPayment = QrisPayment::query()->where('reference_no', $reference)->firstOrFail();

        $this->refreshExpiry($qrisPayment);

        if ($qrisPayment->status === 'paid') {
            return redirect($this->successRedirectUrl($qrisPayment))->with('status', 'Pembayaran sudah diterima. Terima kasih!');
        }

        return view('member.qris-show', [
            'qrisPayment' => $qrisPayment,
            'cancelUrl' => $qrisPayment->flow === 'register' ? route('member.register') : route('member.payment'),
        ]);
    }

    public function status(Request $request, string $reference)
    {
        $qrisPayment = QrisPayment::query()->where('reference_no', $reference)->firstOrFail();

        $this->refreshExpiry($qrisPayment);

        if ($qrisPayment->status === 'pending' && (! $qrisPayment->last_checked_at || $qrisPayment->last_checked_at->diffInSeconds(now()) >= 3)) {
            $this->pollGdcStatus($qrisPayment);

            if ($qrisPayment->status === 'paid') {
                $request->session()->forget('referral_id_reseller');
            }
        }

        return response()->json([
            'status' => $qrisPayment->status,
            'redirect' => $qrisPayment->status === 'paid' ? $this->successRedirectUrl($qrisPayment) : null,
        ]);
    }

    private function createQrisPayment(string $flow, int $amount, array $attributes): ?QrisPayment
    {
        $referenceNo = (string) Str::uuid();
        $externalId = (string) Str::uuid();

        $qrisPayment = QrisPayment::create(array_merge([
            'flow' => $flow,
            'reference_no' => $referenceNo,
            'external_id' => $externalId,
            'amount' => $amount,
            'status' => 'pending',
            'expires_at' => now()->addMinutes((int) config('gdcpay.qr_expiry_minutes')),
        ], $attributes));

        try {
            $result = $this->gdcPay->generateQr($referenceNo, $externalId, $amount);
        } catch (Throwable $exception) {
            report($exception);
            $qrisPayment->delete();

            return null;
        }

        if (empty($result['qrContent'])) {
            $qrisPayment->delete();

            return null;
        }

        $qrisPayment->forceFill([
            'gdc_reference_no' => $result['referenceNo'] ?? null,
            'qr_content' => $result['qrContent'],
        ])->save();

        return $qrisPayment;
    }

    private function pollGdcStatus(QrisPayment $qrisPayment): void
    {
        try {
            $result = $this->gdcPay->queryQr(
                (string) $qrisPayment->gdc_reference_no,
                $qrisPayment->reference_no,
                $qrisPayment->external_id
            );
        } catch (Throwable $exception) {
            report($exception);
            $qrisPayment->forceFill(['last_checked_at' => now()])->save();

            return;
        }

        $qrisPayment->last_checked_at = now();

        $statusCode = (string) ($result['latestTransactionStatus'] ?? '');
        $statusDesc = (string) ($result['transactionStatusDesc'] ?? '');

        if (GdcPayService::isPaidStatus($statusCode, $statusDesc)) {
            $qrisPayment->markAsPaid();

            return;
        }

        if (GdcPayService::isFailedStatus($statusCode, $statusDesc)) {
            $qrisPayment->status = 'failed';
        }

        $qrisPayment->save();
    }

    private function refreshExpiry(QrisPayment $qrisPayment): void
    {
        if ($qrisPayment->status === 'pending' && $qrisPayment->expires_at->isPast()) {
            $qrisPayment->update(['status' => 'expired']);
        }
    }

    private function successRedirectUrl(QrisPayment $qrisPayment): string
    {
        return $qrisPayment->flow === 'register'
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
