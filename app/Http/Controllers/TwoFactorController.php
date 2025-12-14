<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\TwoFactorCodeNotification;


class TwoFactorController extends Controller
{
    public function show()
    {
        // shows the 2FA form
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

    // send email
    $user->notify(new TwoFactorCodeNotification($code));

    // for demo you *can* show the code; remove in production
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

        // mark this session as 2FA verified
        session(['two_factor_verified' => true]);

        // clear used code
        $user->forceFill([
            'two_factor_code'        => null,
            'two_factor_expires_at'  => null,
        ])->save();

        return redirect()->route('account.index')
            ->with('status', 'Two-factor verification successful.');
    }
}
