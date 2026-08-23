<?php

namespace App\Http\Middleware;

use App\Models\Reseller;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureResellerAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $resellerId = (int) $request->session()->get('reseller_id', 0);

        if ($resellerId <= 0) {
            return redirect()->guest(route('reseller.login'));
        }

        $reseller = Reseller::query()->find($resellerId);

        if (!$reseller) {
            $request->session()->forget('reseller_id');

            return redirect()->guest(route('reseller.login'));
        }

        if (! $reseller->is_active) {
            return redirect()->route('reseller.pending');
        }

        $request->attributes->set('reseller', $reseller);

        return $next($request);
    }
}
