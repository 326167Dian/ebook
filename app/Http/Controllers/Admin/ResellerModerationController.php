<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ResellerModerationController extends Controller
{
    public function approve(Reseller $reseller)
    {
        $reseller->update([
            'is_active' => true,
        ]);

        return back()->with('status', 'Reseller berhasil di-approve.');
    }

    public function reject(Reseller $reseller)
    {
        $reseller->update([
            'is_active' => false,
        ]);

        return back()->with('status', 'Reseller ditandai belum aktif.');
    }

    /**
     * Atur kode promo (opsional, alternatif dari ID reseller angka) dan harga
     * khusus (opsional, override harga promo standar - dipakai untuk kode event
     * seperti "JUMAT"). Kosongkan harga khusus untuk kembali ke harga promo standar.
     */
    public function updatePricing(Request $request, Reseller $reseller)
    {
        $data = $request->validate([
            'promo_code' => [
                'nullable', 'string', 'max:20', 'alpha_num',
                Rule::unique('reseller', 'promo_code')->ignore($reseller->id_reseller, 'id_reseller'),
            ],
            'custom_price_final' => ['nullable', 'integer', 'min:0'],
        ]);

        $reseller->update([
            'promo_code' => $data['promo_code'] !== null && $data['promo_code'] !== ''
                ? strtoupper($data['promo_code'])
                : null,
            'custom_price_final' => $data['custom_price_final'] ?? null,
        ]);

        return back()->with('status', 'Kode promo & harga khusus reseller berhasil diperbarui.');
    }
}
