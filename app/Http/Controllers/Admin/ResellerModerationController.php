<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reseller;

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
}
