<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\MetaConversionsApi;

class MemberModerationController extends Controller
{
    public function approve(Member $member, MetaConversionsApi $metaCapi)
    {
        $wasActive = (bool) $member->is_active;

        $member->update([
            'is_active' => true,
            'paid_at' => $member->paid_at ?? now(),
            'payment_rejected_at' => null,
        ]);

        // Pembayaran manual (upload bukti transfer QRIS statis) baru dianggap sah
        // setelah admin approve, jadi Purchase dikirim ke Meta di titik ini.
        $status = 'Member berhasil di-approve dan bisa mengakses e-book.';

        if (! $wasActive && filled($member->payment_proof_path) && ! $member->meta_purchase_sent_at) {
            if ($metaCapi->sendManualPurchase($member)) {
                $status .= ' Konversi Purchase terkirim ke Meta.';
            } elseif ($metaCapi->isConfigured()) {
                $status .= ' (Catatan: konversi ke Meta gagal dikirim, cek storage/logs/laravel.log.)';
            }
        }

        return back()->with('status', $status);
    }

    public function reject(Member $member)
    {
        $member->update([
            'is_active' => false,
            'payment_rejected_at' => now(),
        ]);

        return back()->with('status', 'Member ditandai belum aktif.');
    }
}
