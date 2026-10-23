<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EbookContent;
use App\Models\Member;
use App\Models\Reseller;
use Illuminate\Support\Facades\Schema;

class ApprovalController extends Controller
{
    /**
     * Halaman khusus moderasi member & reseller, dipisah dari halaman edit
     * konten e-book supaya bisa diakses orang lain (yang hanya menangani
     * approval) tanpa mengganggu/terganggu oleh pekerjaan edit isi buku.
     */
    public function index()
    {
        $content = EbookContent::query()->firstOrCreate([], EbookContent::defaultData());

        $pendingMembers = collect();

        if (Schema::hasTable('members')) {
            $pendingMembers = Member::query()
                ->where('is_active', false)
                ->whereNotNull('payment_proof_path')
                ->where('payment_proof_path', '!=', '')
                ->latest()
                ->get();
        }

        // Calon pembeli: sudah isi data (langkah 1) tapi belum upload bukti bayar.
        $leadMembers = collect();

        if (Schema::hasTable('members')) {
            $leadMembers = Member::query()
                ->where('is_active', false)
                ->where(fn ($q) => $q->whereNull('payment_proof_path')->orWhere('payment_proof_path', ''))
                ->where('created_at', '>=', now()->subDays(30))
                ->latest()
                ->limit(50)
                ->get();
        }

        $pendingResellers = collect();

        if (Schema::hasTable('reseller')) {
            $pendingResellers = Reseller::query()->where('is_active', false)->latest()->get();
        }

        return view('admin.approval', [
            'content' => $content,
            'pendingMembers' => $pendingMembers,
            'leadMembers' => $leadMembers,
            'pendingResellers' => $pendingResellers,
        ]);
    }
}
