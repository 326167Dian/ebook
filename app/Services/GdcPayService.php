<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GdcPayService
{
    private array $config;

    /**
     * Full detail (URL, headers, body) of the most recent outgoing request made
     * by rawSnapCall()/rawAccessTokenCall(), so callers like the GDC UAT runner
     * can report the exact Request alongside the Response.
     *
     * @var array{url: string, headers: array<string, string>, body: string}|null
     */
    public ?array $lastRequestDebug = null;

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

    public function balanceInquiry(string $partnerReferenceNo): array
    {
        return $this->snapPost('/v1.0/balance-inquiry', [
            'partnerReferenceNo' => $partnerReferenceNo,
            'additionalInfo' => (object) [],
        ], (string) Str::uuid());
    }

    public function bankAccountInquiry(string $partnerReferenceNo, int $amount, string $beneficiaryAccountNumber, string $beneficiaryBankCode): array
    {
        return $this->snapPost('/v1.0/emoney/bank-account-inquiry', [
            'partnerReferenceNo' => $partnerReferenceNo,
            'customerNumber' => $this->config['account_number'],
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'beneficiaryAccountNumber' => $beneficiaryAccountNumber,
            'additionalInfo' => [
                'beneficiaryBankCode' => $beneficiaryBankCode,
                'remarks' => 'UAT test',
            ],
        ], (string) Str::uuid());
    }

    public function transferBank(string $partnerReferenceNo, int $amount, string $beneficiaryAccountNumber, string $beneficiaryBankCode): array
    {
        return $this->snapPost('/v1.0/emoney/transfer-bank', [
            'partnerReferenceNo' => $partnerReferenceNo,
            'customerNumber' => $this->config['account_number'],
            'amount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'beneficiaryAccountNumber' => $beneficiaryAccountNumber,
            'beneficiaryBankCode' => $beneficiaryBankCode,
            'additionalInfo' => [
                'remarks' => 'UAT test',
            ],
        ], (string) Str::uuid());
    }

    public function createVirtualAccount(string $trxId, int $amount, string $virtualAccountName): array
    {
        return $this->snapPost('/v1.0/transfer-va/create-va', [
            'virtualAccountName' => $virtualAccountName,
            'expiredDate' => now()->addDay()->format('Y-m-d\TH:i:sP'),
            'trxId' => $trxId,
            'virtualAccountTrxType' => 'C',
            'totalAmount' => [
                'value' => number_format($amount, 2, '.', ''),
                'currency' => 'IDR',
            ],
            'additionalInfo' => [
                'accountNumber' => $this->config['account_number'],
                'bankCode' => '014',
            ],
        ], (string) Str::uuid());
    }

    public function vaStatus(string $virtualAccountNo, string $paymentRequestId): array
    {
        return $this->snapPost('/v1.0/transfer-va/status', [
            'virtualAccountNo' => $virtualAccountNo,
            'paymentRequestId' => $paymentRequestId,
            'inquiryRequestId' => '',
        ], (string) Str::uuid());
    }

    public function transactionHistoryList(string $fromDateTime, string $toDateTime, int $pageSize = 5, int $pageNumber = 1): array
    {
        return $this->snapPost('/v1.0/transaction-history-list', [
            'fromDateTime' => $fromDateTime,
            'toDateTime' => $toDateTime,
            'pageSize' => $pageSize,
            'pageNumber' => $pageNumber,
            'additionalInfo' => [
                'status' => '',
                'type' => 'PAYMENT',
            ],
        ], (string) Str::uuid());
    }

    public function transactionHistoryDetail(string $originalPartnerReferenceNo): array
    {
        return $this->snapPost('/v1.0/transaction-history-detail', [
            'originalPartnerReferenceNo' => $originalPartnerReferenceNo,
            'originalReferenceNo' => '',
            'additionalInfo' => (object) [],
        ], (string) Str::uuid());
    }

    private function snapPost(string $path, array $body, string $externalId): array
    {
        $response = $this->rawSnapCall($path, $body, $externalId);

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
     * Low-level SNAP call that returns the raw HTTP response instead of throwing,
     * so callers (like the GDC UAT test-case runner) can inspect both success and
     * error responses faithfully. Overrides exist only to reproduce negative test
     * scenarios (bad token, bad signature) required by GDC's certification suite.
     */
    public function rawSnapCall(
        string $path,
        array $body,
        string $externalId,
        ?string $tokenOverride = null,
        ?string $signatureOverride = null,
    ): Response {
        $token = $tokenOverride ?? $this->getAccessToken();
        $timestamp = now()->format('Y-m-d\TH:i:sP');
        $payloadJson = json_encode($body, JSON_UNESCAPED_SLASHES);

        // SNAP symmetric signature: HMAC-SHA512 keyed with the client secret,
        // per neo-docs.gdc.co.id "<METHOD>:<PATH>:<TOKEN>:<PAYLOAD_HASH>:<TIMESTAMP>".
        // PATH here is the path without the /snap prefix, matching the docs example.
        $payloadHash = hash('sha256', $payloadJson);
        $stringToSign = "POST:{$path}:{$token}:{$payloadHash}:{$timestamp}";
        $signature = $signatureOverride ?? base64_encode(hash_hmac('sha512', $stringToSign, $this->config['client_secret'], true));

        $headers = array_filter([
            'Authorization' => 'Bearer ' . $token,
            'X-TIMESTAMP' => $timestamp,
            'X-PARTNER-ID' => $this->config['partner_id'],
            'X-EXTERNAL-ID' => $externalId,
            'X-SIGNATURE' => $signature,
            'CHANNEL-ID' => $this->config['channel_id'] ?: null,
        ]);

        $this->lastRequestDebug = [
            'url' => rtrim($this->config['base_url'], '/') . '/snap' . $path,
            'headers' => array_merge(['Content-Type' => 'application/json'], $headers),
            'body' => $payloadJson,
        ];

        return Http::baseUrl($this->config['base_url'])
            ->withHeaders($headers)
            ->withBody($payloadJson, 'application/json')
            ->post('/snap' . $path);
    }

    /**
     * Low-level access-token call that returns the raw HTTP response instead of
     * throwing. Overrides exist only to reproduce the "invalid client" and
     * "invalid signature" negative test cases required by GDC's certification suite.
     */
    public function rawAccessTokenCall(?string $clientKeyOverride = null, ?string $signatureOverride = null): Response
    {
        $clientKey = $clientKeyOverride ?? $this->config['client_key'];
        $timestamp = now()->format('Y-m-d\TH:i:sP');

        if ($signatureOverride !== null) {
            $signatureBase64 = $signatureOverride;
        } else {
            $stringToSign = $clientKey . '|' . $timestamp;
            $privateKey = openssl_pkey_get_private(file_get_contents($this->config['private_key_path']));
            openssl_sign($stringToSign, $signature, $privateKey, OPENSSL_ALGO_SHA256);
            $signatureBase64 = base64_encode($signature);
        }

        $headers = [
            'Content-Type' => 'application/json',
            'X-Timestamp' => $timestamp,
            'Client-Key' => $clientKey,
            'X-Signature' => $signatureBase64,
        ];
        $body = ['grantType' => 'client_credentials'];

        $this->lastRequestDebug = [
            'url' => rtrim($this->config['base_url'], '/') . '/snap/v1.0/access-token/b2b',
            'headers' => $headers,
            'body' => json_encode($body, JSON_UNESCAPED_SLASHES),
        ];

        return Http::baseUrl($this->config['base_url'])
            ->withHeaders($headers)
            ->post('/snap/v1.0/access-token/b2b', $body);
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
            $response = $this->rawAccessTokenCall();

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
