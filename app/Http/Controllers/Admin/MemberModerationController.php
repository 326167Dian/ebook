<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Member;

class MemberModerationController extends Controller
{
    public function approve(Member $member)
    {
        $member->update([
            'is_active' => true,
            'paid_at' => $member->paid_at ?? now(),
        ]);

        return back()->with('status', 'Member berhasil di-approve dan bisa mengakses e-book.');
    }

    public function reject(Member $member)
    {
        $member->update([
            'is_active' => false,
        ]);

        return back()->with('status', 'Member ditandai belum aktif.');
    }
}
