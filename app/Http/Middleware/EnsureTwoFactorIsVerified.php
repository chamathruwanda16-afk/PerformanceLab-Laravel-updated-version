<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsVerified
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only check if user is logged in
        if ($request->user() && ! session('two_factor_verified')) {
            // Not verified yet → send to 2FA page
            return redirect()->route('twofactor.show');
        }

        return $next($request);
    }
}
