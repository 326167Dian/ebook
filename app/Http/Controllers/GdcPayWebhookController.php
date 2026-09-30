<?php

namespace App\Http\Controllers;

use App\Models\QrisPayment;
use App\Services\GdcPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GdcPayWebhookController extends Controller
{
    /**
     * Receives the "QRIS Payment Notification" server-to-server callback from GDC Pay.
     *
     * The exact payload shape is not documented (neo-docs.gdc.co.id only documents the
     * qr-mpm-generate/qr-mpm-query request/response bodies), so this logs the raw body
     * on every call and matches loosely against the field names used by those endpoints
     * (partnerReferenceNo/referenceNo + latestTransactionStatus/transactionStatusDesc).
     * Adjust the field lookups below once a real sandbox notification has been observed
     * in the log.
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
            $qrisPayment->markAsPaid();
        } elseif (GdcPayService::isFailedStatus($statusCode, $statusDesc) && $qrisPayment->status === 'pending') {
            $qrisPayment->update(['status' => 'failed']);
        }

        return response()->json(['responseCode' => '2005600', 'responseMessage' => 'successful']);
    }
}
