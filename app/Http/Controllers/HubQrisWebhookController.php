<?php

namespace App\Http\Controllers;

use App\Models\QrisPayment;
use Illuminate\Http\Request;

/**
 * Menerima notifikasi yang DITERUSKAN hub (mysifacenter.my.id) -- bukan dari GDC
 * langsung lagi (situs ini bukan partner GDC terdaftar sendiri, lihat
 * HubQrisService). GdcPayWebhookController (lama, menerima panggilan GDC
 * langsung) dibiarkan apa adanya untuk transaksi yang sudah terlanjur dibuat
 * lewat jalur langsung sebelum konversi ke hub ini.
 */
class HubQrisWebhookController extends Controller
{
    public function notification(Request $request)
    {
        $key = (string) $request->header('X-Branch-Key', '');

        abort_unless($key !== '' && hash_equals((string) config('hubqris.api_key'), $key), 401);

        $qrisPayment = QrisPayment::where('reference_no', (string) $request->input('reference_no'))->first();

        abort_unless($qrisPayment, 404);

        $status = (string) $request->input('status');

        if ($status === 'paid') {
            $qrisPayment->markAsPaid();
        } elseif (in_array($status, ['failed', 'expired'], true) && $qrisPayment->status === 'pending') {
            $qrisPayment->update(['status' => $status]);
        }

        return response()->json(['status' => 'ok']);
    }
}
