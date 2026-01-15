<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TwoFactorController extends Controller
{
    public function show()
    {
        return view('auth.two-factor');
    }

    public function send(Request $request)
    {
        $user = $request->user();

        // generate 6-digit code
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // save code + expiry
        $user->forceFill([
            'two_factor_code'       => $code,
            'two_factor_expires_at' => now()->addMinutes(10),
        ])->save();

        // ✅ Send via Mailtrap HTTP API (works on Render)
        $response = Http::withToken(config('services.mailtrap.token'))
            ->post('https://send.api.mailtrap.io/api/send', [
                "from" => [
                    "email" => env('MAIL_FROM_ADDRESS', 'no-reply@performancelab.com'),
                    "name"  => env('MAIL_FROM_NAME', 'PerformanceLab'),
                ],
                "to" => [
                    ["email" => $user->email],
                ],
                "subject" => "Your verification code",
                "text"    => "Your verification code is: {$code}",
            ]);

        // Optional: if API fails, show error instead of silently failing
        if (! $response->successful()) {
            return back()->withErrors([
                'email' => 'Could not send verification email. Please try again.',
            ]);
        }

        return back()->with('success', 'Verification code sent to your email.');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'code' => 'required|digits:6',
        ]);

        $user = $request->user();

        if (
            ! $user->two_factor_code ||
            ! $user->two_factor_expires_at ||
            $user->two_factor_expires_at->isPast() ||
            $request->code !== $user->two_factor_code
        ) {
            return back()->withErrors([
                'code' => 'The code is invalid or has expired.',
            ]);
        }

        session(['two_factor_verified' => true]);

        $user->forceFill([
            'two_factor_code'       => null,
            'two_factor_expires_at' => null,
        ])->save();

        return redirect()->route('account.index')
            ->with('status', 'Two-factor verification successful.');
    }
}
