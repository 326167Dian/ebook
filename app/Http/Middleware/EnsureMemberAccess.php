<?php

namespace App\Http\Middleware;

use App\Models\Member;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMemberAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $memberId = (int) $request->session()->get('member_id', 0);

        if ($memberId <= 0) {
            return redirect()->guest(route('member.login'));
        }

        $member = Member::query()
            ->whereKey($memberId)
            ->where('is_active', true)
            ->first();

        if (!$member) {
            $request->session()->forget('member_id');

            return redirect()->guest(route('member.login'));
        }

        $request->attributes->set('member', $member);

        return $next($request);
    }
}
