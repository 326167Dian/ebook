<?php

namespace App\Http\Controllers;

use App\Models\EbookContent;
use App\Models\Member;
use App\Models\Reseller;
use App\Models\QrisPayment;
use App\Services\HubQrisService;
use Endroid\QrCode\Builder\Builder;
use App\Rules\ValidReferralCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class QrisPaymentController extends Controller
{
    public function __construct(private readonly HubQrisService $hubQris)
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

        $qrisPayment = $this->createQrisPayment('register', $amount, [
            'pending_name' => $data['name'],
            'pending_email' => $data['email'],
            'id_reseller' => $data['referral_code'],
        ]);

        if (! $qrisPayment) {
            // QRIS Dinamis gagal -- jatuh otomatis ke QRIS Statis (lalu transfer bank kalau
            // itu pun tidak dikonfigurasi), bukan menampilkan error ke customer. Lihat
            // showRegister()/register.blade.php untuk cabang tampilan fallback-nya.
            return back()->withInput()->with('qris_fallback', true);
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
            'referral_code' => ['nullable', 'string', 'max:20', new ValidReferralCode()],
        ]);

        $data['referral_code'] = Reseller::idFromReferralCode($data['referral_code'] ?? null);

        $amount = EbookContent::priceFor($data['referral_code']);

        $qrisPayment = $this->createQrisPayment('payment', $amount, [
            'member_id' => $member->id,
            'id_reseller' => $data['referral_code'],
        ]);

        if (! $qrisPayment) {
            return back()->withInput()->with('qris_fallback', true);
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

        $qrDataUri = null;

        if ($qrisPayment->status === 'pending' && $qrisPayment->qr_content) {
            try {
                $qrDataUri = (new Builder())->build(data: $qrisPayment->qr_content, size: 320, margin: 10)->getDataUri();
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        return view('member.qris-show', [
            'qrisPayment' => $qrisPayment,
            'qrDataUri' => $qrDataUri,
            'cancelUrl' => $qrisPayment->flow === 'register' ? route('member.register') : route('member.payment'),
        ]);
    }

    public function status(Request $request, string $reference)
    {
        $qrisPayment = QrisPayment::query()->where('reference_no', $reference)->firstOrFail();

        $this->refreshExpiry($qrisPayment);

        if ($qrisPayment->status === 'pending' && (! $qrisPayment->last_checked_at || $qrisPayment->last_checked_at->diffInSeconds(now()) >= 3)) {
            $this->pollHubStatus($qrisPayment);

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

        $qrisPayment = QrisPayment::create(array_merge([
            'flow' => $flow,
            'reference_no' => $referenceNo,
            'external_id' => $referenceNo,
            'amount' => $amount,
            'status' => 'pending',
            'expires_at' => now()->addMinutes((int) config('hubqris.qr_expiry_minutes')),
        ], $attributes));

        try {
            $result = $this->hubQris->generateQr($referenceNo, $amount);
        } catch (Throwable $exception) {
            report($exception);
            $qrisPayment->delete();

            return null;
        }

        if (empty($result['qr_content'])) {
            $qrisPayment->delete();

            return null;
        }

        $qrisPayment->forceFill([
            'qr_content' => $result['qr_content'],
        ])->save();

        return $qrisPayment;
    }

    /**
     * Beda dari pollGdcStatus() (lama, langsung ke GDC): hub sudah memverifikasi
     * nominal sendiri sebelum menandai 'paid' (lihat BranchQrisPayment::
     * receivedAmountMatches() di proyek hub), jadi di sini cukup percaya status
     * yang dibalas hub, tidak perlu verifikasi nominal ulang.
     */
    private function pollHubStatus(QrisPayment $qrisPayment): void
    {
        try {
            $result = $this->hubQris->queryStatus($qrisPayment->reference_no);
        } catch (Throwable $exception) {
            report($exception);
            $qrisPayment->forceFill(['last_checked_at' => now()])->save();

            return;
        }

        $qrisPayment->last_checked_at = now();

        if (($result['status'] ?? '') === 'paid') {
            $qrisPayment->markAsPaid();

            return;
        }

        if (in_array($result['status'] ?? '', ['failed', 'expired'], true)) {
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
