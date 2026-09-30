<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GdcPayService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('gdcpay');
    }

    public function generateQr(string $partnerReferenceNo, string $externalId, int $amount): array
    {
        return $this->snapPost('/v1.0/qr/qr-mpm-generate', [
            'partnerReferenceNo' => $partnerReferenceNo,
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'merchantId' => $this->config['merchant_id'],
            'additionalInfo' => [
                'accountNumber' => $this->config['account_number'],
            ],
        ], $externalId);
    }

    public function queryQr(string $originalReferenceNo, string $originalPartnerReferenceNo, string $originalExternalId): array
    {
        return $this->snapPost('/v1.0/qr/qr-mpm-query', [
            'originalReferenceNo' => $originalReferenceNo,
            'originalPartnerReferenceNo' => $originalPartnerReferenceNo,
            'originalExternalId' => $originalExternalId,
            'serviceCode' => '47',
            'additionalInfo' => (object) [],
        ], (string) Str::uuid());
    }

    private function snapPost(string $path, array $body, string $externalId): array
    {
        $token = $this->getAccessToken();
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        $payloadJson = json_encode($body, JSON_UNESCAPED_SLASHES);
        $payloadHash = hash('sha256', $payloadJson);

        // SNAP symmetric signature: HMAC-SHA512 keyed with the client secret,
        // per neo-docs.gdc.co.id "<METHOD>:<PATH>:<TOKEN>:<PAYLOAD_HASH>:<TIMESTAMP>".
        // PATH here is the path without the /snap prefix, matching the docs example.
        $stringToSign = "POST:{$path}:{$token}:{$payloadHash}:{$timestamp}";
        $signature = base64_encode(hash_hmac('sha512', $stringToSign, $this->config['client_secret'], true));

        $response = Http::baseUrl($this->config['base_url'])
            ->withHeaders(array_filter([
                'Authorization' => 'Bearer ' . $token,
                'X-TIMESTAMP' => $timestamp,
                'X-PARTNER-ID' => $this->config['partner_id'],
                'X-EXTERNAL-ID' => $externalId,
                'X-SIGNATURE' => $signature,
                'CHANNEL-ID' => $this->config['channel_id'] ?: null,
            ]))
            ->withBody($payloadJson, 'application/json')
            ->post('/snap' . $path);

        if ($response->failed()) {
            throw new RuntimeException("GDC Pay: HTTP {$response->status()} pada {$path} - {$response->body()}");
        }

        $data = $response->json();
        $responseCode = (string) ($data['responseCode'] ?? '');

        if ($responseCode !== '' && ! str_starts_with($responseCode, '200')) {
            throw new RuntimeException('GDC Pay: ' . ($data['responseMessage'] ?? 'Unknown error') . " ({$responseCode})");
        }

        return $data ?? [];
    }

    /**
     * The docs only confirm code "03" = Pending; success/failure codes for QRIS
     * transactions are not documented, so we match on the description too.
     */
    public static function isPaidStatus(string $statusCode, string $statusDesc): bool
    {
        $statusDesc = strtolower($statusDesc);

        return $statusCode === '00'
            || str_contains($statusDesc, 'success')
            || str_contains($statusDesc, 'settlement')
            || str_contains($statusDesc, 'paid');
    }

    public static function isFailedStatus(string $statusCode, string $statusDesc): bool
    {
        $statusDesc = strtolower($statusDesc);

        return str_contains($statusDesc, 'fail')
            || str_contains($statusDesc, 'reject')
            || str_contains($statusDesc, 'cancel');
    }

    public function getAccessToken(): string
    {
        return Cache::remember('gdcpay_access_token', 750, function () {
            $timestamp = now()->format('Y-m-d\TH:i:sP');
            $stringToSign = $this->config['client_key'] . '|' . $timestamp;

            $privateKeyPath = $this->config['private_key_path'];
            if (! $privateKeyPath || ! is_file($privateKeyPath)) {
                throw new RuntimeException("GDC Pay: file private key tidak ditemukan ({$privateKeyPath}). Set GDCPAY_PRIVATE_KEY_PATH di .env.");
            }

            $privateKey = openssl_pkey_get_private(file_get_contents($privateKeyPath));
            if (! $privateKey) {
                throw new RuntimeException('GDC Pay: private key tidak valid, periksa isi file PEM.');
            }

            // Asymmetric signature (RSA-SHA256 / PKCS#1 v1.5) over "clientKey|timestamp".
            openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);

            $response = Http::baseUrl($this->config['base_url'])
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Timestamp' => $timestamp,
                    'Client-Key' => $this->config['client_key'],
                    'X-Signature' => base64_encode($signature),
                ])
                ->post('/snap/v1.0/access-token/b2b', [
                    'grantType' => 'client_credentials',
                ]);

            if ($response->failed()) {
                throw new RuntimeException("GDC Pay: gagal mengambil access token (HTTP {$response->status()}) - {$response->body()}");
            }

            $accessToken = $response->json('accessToken');

            if (! $accessToken) {
                throw new RuntimeException('GDC Pay: accessToken tidak ditemukan pada respons access-token.');
            }

            return $accessToken;
        });
    }
}
