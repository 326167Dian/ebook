<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    private array $config;

    public function __construct()
    {
        $this->config = config('midtrans');
    }

    private function baseUrl(): string
    {
        return $this->config['is_production']
            ? 'https://api.midtrans.com'
            : 'https://api.sandbox.midtrans.com';
    }

    /**
     * Creates a QRIS charge via the Core API. Docs: https://docs.midtrans.com/reference/qris
     */
    public function createQrisCharge(string $orderId, int $amount, int $expiryMinutes): array
    {
        $response = Http::withBasicAuth($this->config['server_key'], '')
            ->acceptJson()
            ->asJson()
            ->post($this->baseUrl() . '/v2/charge', [
                'payment_type' => 'qris',
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => $amount,
                ],
                'qris' => [
                    'acquirer' => 'gopay',
                ],
                'custom_expiry' => [
                    'expiry_duration' => $expiryMinutes,
                    'unit' => 'minute',
                ],
            ]);

        $data = $response->json();

        // Midtrans returns HTTP 200/201 even for business-logic errors (e.g. an
        // inactive payment channel) - the real result is in status_code.
        $statusCode = (string) ($data['status_code'] ?? '');

        if ($response->failed() || ! in_array($statusCode, ['200', '201'], true)) {
            throw new RuntimeException('Midtrans: ' . ($data['status_message'] ?? 'HTTP ' . $response->status()) . " (status_code: {$statusCode})");
        }

        $qrAction = collect($data['actions'] ?? [])->firstWhere('name', 'generate-qr-code');

        return [
            'transaction_id' => $data['transaction_id'] ?? null,
            'qr_string' => $data['qr_string'] ?? null,
            'qr_image_url' => $qrAction['url'] ?? null,
        ];
    }

    /**
     * Checks the current status of a transaction. Docs: https://docs.midtrans.com/reference/get-transaction-status
     */
    public function getStatus(string $orderId): array
    {
        $response = Http::withBasicAuth($this->config['server_key'], '')
            ->acceptJson()
            ->get($this->baseUrl() . "/v2/{$orderId}/status");

        $data = $response->json();

        if ($response->failed() && ($data['status_code'] ?? null) !== '404') {
            throw new RuntimeException('Midtrans: ' . ($data['status_message'] ?? 'HTTP ' . $response->status()));
        }

        return $data ?? [];
    }

    /**
     * Transaction statuses that mean the payment is settled.
     * Docs: https://docs.midtrans.com/docs/https-notification-webhooks
     */
    public static function isPaidStatus(string $transactionStatus): bool
    {
        return in_array($transactionStatus, ['settlement', 'capture'], true);
    }

    public static function isFailedStatus(string $transactionStatus): bool
    {
        return in_array($transactionStatus, ['deny', 'cancel', 'expire', 'failure'], true);
    }

    /**
     * Verifies the "signature_key" sent with every HTTP notification.
     * Formula: SHA512(order_id + status_code + gross_amount + ServerKey)
     * Docs: https://docs.midtrans.com/docs/https-notification-webhooks
     */
    public function verifyNotificationSignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $expected = hash('sha512', $orderId . $statusCode . $grossAmount . $this->config['server_key']);

        return hash_equals($expected, $signatureKey);
    }
}
