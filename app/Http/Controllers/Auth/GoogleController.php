<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    public function redirect()
    {
        // ✅ stateless avoids "InvalidStateException" on hosted environments
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        // ✅ stateless here too
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?? 'Google User',
                'password' => bcrypt(Str::random(16)),
                'email_verified_at' => now(),
            ]
        );

        Auth::login($user, true);

        // ✅ BYPASS 2FA only for Google login (session flag)
        session(['two_factor_verified' => true]);

        return redirect()->route('dashboard');
    }
}
