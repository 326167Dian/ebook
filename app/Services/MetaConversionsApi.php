<?php

namespace App\Services;

use App\Models\EbookContent;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Kirim event ke Meta lewat Conversions API (server-to-server).
 *
 * Dipakai untuk pembayaran manual QRIS statis: saat konsumen upload bukti transfer
 * kita simpan cookie _fbp/_fbc + IP + user agent-nya, lalu saat admin klik Approve
 * event "Purchase" dikirim dari server dengan data tersebut.
 */
class MetaConversionsApi
{
    public const PAYMENT_METHOD_MANUAL = 'qris_manual';

    public const CONTENT_NAME = 'E-Book Bisnis Apotek Itu Asik';

    /**
     * Data browser yang perlu disimpan ke member saat upload bukti transfer.
     */
    public static function trackingFromRequest(Request $request): array
    {
        return [
            'meta_fbp' => self::cleanCookie($request->cookie('_fbp')),
            'meta_fbc' => self::cleanCookie($request->cookie('_fbc')) ?? self::fbcFromReferer($request),
            'meta_client_ip' => $request->ip(),
            'meta_user_agent' => substr((string) $request->userAgent(), 0, 1000) ?: null,
            'meta_source_url' => substr((string) ($request->headers->get('referer') ?: $request->fullUrl()), 0, 500),
        ];
    }

    public function isConfigured(): bool
    {
        return filled(config('services.meta_pixel.id')) && filled(config('services.meta_pixel.capi_token'));
    }

    /**
     * Kirim Purchase untuk member yang pembayaran manualnya baru di-approve admin.
     * Tidak pernah melempar exception: kegagalan hanya dicatat di log supaya proses
     * approve tetap berjalan.
     */
    public function sendManualPurchase(Member $member): bool
    {
        if (! $this->isConfigured() || $member->meta_purchase_sent_at) {
            return false;
        }

        $value = (int) (EbookContent::query()->value('payment_price_final') ?? 0);

        $userData = array_filter([
            'em' => [$this->hash(strtolower(trim((string) $member->email)))],
            'external_id' => [$this->hash('member-' . $member->id)],
            'country' => [$this->hash('id')],
            'fbp' => $member->meta_fbp,
            'fbc' => $member->meta_fbc,
            'client_ip_address' => $member->meta_client_ip,
            'client_user_agent' => $member->meta_user_agent,
        ], fn ($v) => filled($v));

        $nameParts = preg_split('/\s+/', trim((string) $member->name)) ?: [];
        if (! empty($nameParts[0])) {
            $userData['fn'] = [$this->hash(mb_strtolower($nameParts[0]))];
        }
        if (count($nameParts) > 1) {
            $userData['ln'] = [$this->hash(mb_strtolower(end($nameParts)))];
        }

        $event = [
            'event_name' => 'Purchase',
            'event_time' => now()->timestamp,
            'event_id' => 'purchase-member-' . $member->id,
            'action_source' => 'website',
            'event_source_url' => $member->meta_source_url ?: url('/member/payment'),
            'user_data' => $userData,
            'custom_data' => [
                'value' => $value,
                'currency' => 'IDR',
                'content_name' => self::CONTENT_NAME,
                'content_type' => 'product',
                'payment_method' => self::PAYMENT_METHOD_MANUAL,
            ],
        ];

        $payload = [
            'data' => [$event],
            'access_token' => config('services.meta_pixel.capi_token'),
        ];

        if (filled(config('services.meta_pixel.test_event_code'))) {
            $payload['test_event_code'] = config('services.meta_pixel.test_event_code');
        }

        $url = sprintf(
            'https://graph.facebook.com/%s/%s/events',
            config('services.meta_pixel.graph_version', 'v25.0'),
            config('services.meta_pixel.id')
        );

        try {
            $response = Http::asJson()->timeout(10)->post($url, $payload);
        } catch (Throwable $e) {
            Log::warning('Meta CAPI Purchase gagal dikirim', ['member_id' => $member->id, 'error' => $e->getMessage()]);

            return false;
        }

        if (! $response->successful()) {
            Log::warning('Meta CAPI Purchase ditolak', [
                'member_id' => $member->id,
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body(),
            ]);

            return false;
        }

        $member->forceFill(['meta_purchase_sent_at' => now()])->save();

        Log::info('Meta CAPI Purchase terkirim', ['member_id' => $member->id, 'value' => $value]);

        return true;
    }

    private function hash(string $value): string
    {
        return hash('sha256', $value);
    }

    private static function cleanCookie(mixed $value): ?string
    {
        $value = is_string($value) ? trim($value) : '';

        return str_starts_with($value, 'fb.') ? substr($value, 0, 255) : null;
    }

    /**
     * Cadangan bila cookie _fbc belum ada: bentuk dari parameter fbclid di URL
     * halaman sebelumnya (format fb.1.<timestamp ms>.<fbclid>).
     */
    private static function fbcFromReferer(Request $request): ?string
    {
        $fbclid = $request->query('fbclid');

        if (! $fbclid && ($referer = $request->headers->get('referer'))) {
            parse_str((string) parse_url($referer, PHP_URL_QUERY), $query);
            $fbclid = $query['fbclid'] ?? null;
        }

        return is_string($fbclid) && $fbclid !== ''
            ? substr('fb.1.' . now()->getTimestampMs() . '.' . $fbclid, 0, 255)
            : null;
    }
}
