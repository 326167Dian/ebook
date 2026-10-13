<?php

namespace App\Http\Controllers;

use App\Models\QrisPayment;
use App\Services\GdcPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class GdcPayWebhookController extends Controller
{
    /**
     * SNAP requires GDC to obtain its own access token from us ("B2B" token endpoint,
     * mirrored at /v1.0/access-token/b2b on our domain) before it will POST the actual
     * QRIS payment notification. Discovered empirically: GDC's notifier called this exact
     * path at our webhook domain's root and gave up with no retry once it got a 404, which
     * is the real cause of "callback belum di implementasi oleh partner". We don't hold
     * GDC's public key to verify their signature, so for now we log the request for later
     * hardening and issue an opaque bearer token GDC can echo back on the notification call
     * (qrisNotification() below does not currently validate that token).
     */
    public function issueAccessToken(Request $request)
    {
        Log::info('gdcpay.access_token.requested', [
            'payload' => $request->all(),
            'headers' => $request->headers->all(),
        ]);

        return response()->json([
            'responseCode' => '2007300',
            'responseMessage' => 'Successful',
            'accessToken' => (string) Str::uuid(),
            'tokenType' => 'Bearer',
            'expiresIn' => '900',
        ]);
    }

    /**
     * Receives the "QRIS Payment Notification" server-to-server callback from GDC Pay.
     *
     * Field lookups below are confirmed against a real sandbox notification observed in
     * the log (2026-10-13), e.g.:
     * {"originalPartnerReferenceNo":"...","originalReferenceNo":"...","latestTransactionStatus":"00",
     *  "transactionStatusDesc":"SUCCESS","paidAmount":{"value":"10000.00","currency":"IDR"},
     *  "feeAmount":{...},"additionalInfo":{"paymentDate":"...","paymentMethod":"qrisac","transactionType":"deposit"}}
     * Notably the paid amount comes in "paidAmount", not "amount" like the qr-mpm-query response.
     */
    public function qrisNotification(Request $request)
    {
        $payload = $request->all();

        Log::info('gdcpay.qris_notification.received', [
            'payload' => $payload,
            'headers' => $request->headers->all(),
        ]);

        $reference = $payload['originalPartnerReferenceNo']
            ?? $payload['partnerReferenceNo']
            ?? $payload['referenceNo']
            ?? $payload['originalReferenceNo']
            ?? null;

        if (! $reference) {
            Log::warning('gdcpay.qris_notification.no_reference', ['payload' => $payload]);

            return response()->json(['responseCode' => '4005601', 'responseMessage' => 'Invalid reference'], 400);
        }

        $qrisPayment = QrisPayment::query()
            ->where('reference_no', $reference)
            ->orWhere('gdc_reference_no', $reference)
            ->first();

        if (! $qrisPayment) {
            Log::warning('gdcpay.qris_notification.not_found', ['reference' => $reference]);

            return response()->json(['responseCode' => '4045601', 'responseMessage' => 'Transaction not found'], 404);
        }

        $statusCode = (string) ($payload['latestTransactionStatus'] ?? $payload['transactionStatus'] ?? $payload['status'] ?? '');
        $statusDesc = (string) ($payload['transactionStatusDesc'] ?? $payload['statusDesc'] ?? '');

        if (GdcPayService::isPaidStatus($statusCode, $statusDesc)) {
            $receivedAmount = $payload['paidAmount'] ?? $payload['amount'] ?? null;

            if (! $qrisPayment->receivedAmountMatches($receivedAmount)) {
                Log::warning('gdcpay.qris_notification.amount_mismatch', [
                    'qris_payment_id' => $qrisPayment->id,
                    'reference_no' => $qrisPayment->reference_no,
                    'expected_amount' => $qrisPayment->amount,
                    'received_amount' => $receivedAmount,
                ]);

                if ($qrisPayment->status === 'pending') {
                    $qrisPayment->update(['status' => 'failed']);
                }

                return response()->json(['responseCode' => '2005600', 'responseMessage' => 'successful']);
            }

            $qrisPayment->markAsPaid();
        } elseif (GdcPayService::isFailedStatus($statusCode, $statusDesc) && $qrisPayment->status === 'pending') {
            $qrisPayment->update(['status' => 'failed']);
        }

        return response()->json(['responseCode' => '2005600', 'responseMessage' => 'successful']);
    }
}
