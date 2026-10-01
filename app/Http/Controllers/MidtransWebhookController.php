<?php

namespace App\Http\Controllers;

use App\Models\MidtransPayment;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransWebhookController extends Controller
{
    public function __construct(private readonly MidtransService $midtrans)
    {
    }

    /**
     * Receives Midtrans's HTTP notification, sent whenever a transaction's status changes.
     * Docs: https://docs.midtrans.com/docs/https-notification-webhooks
     */
    public function notification(Request $request)
    {
        $payload = $request->all();

        Log::info('midtrans.notification.received', ['payload' => $payload]);

        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');

        if ($orderId === '' || ! $this->midtrans->verifyNotificationSignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
            Log::warning('midtrans.notification.invalid_signature', ['order_id' => $orderId]);

            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = MidtransPayment::query()->where('order_id', $orderId)->first();

        if (! $payment) {
            Log::warning('midtrans.notification.not_found', ['order_id' => $orderId]);

            return response()->json(['message' => 'Transaction not found'], 404);
        }

        $transactionStatus = (string) ($payload['transaction_status'] ?? '');

        if (MidtransService::isPaidStatus($transactionStatus)) {
            if (! $payment->receivedAmountMatches($grossAmount)) {
                Log::warning('midtrans.notification.amount_mismatch', [
                    'midtrans_payment_id' => $payment->id,
                    'order_id' => $orderId,
                    'expected_amount' => $payment->amount,
                    'received_amount' => $grossAmount,
                ]);

                if ($payment->status === 'pending') {
                    $payment->update(['status' => 'failed']);
                }

                return response()->json(['message' => 'OK']);
            }

            $payment->markAsPaid();
        } elseif (MidtransService::isFailedStatus($transactionStatus) && $payment->status === 'pending') {
            $payment->update(['status' => 'failed']);
        }

        return response()->json(['message' => 'OK']);
    }
}
