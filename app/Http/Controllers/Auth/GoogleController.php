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
        // ✅ MUST be stateless here too (Render/session issues)
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::updateOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'name' => $googleUser->getName() ?? 'Google User',
                'password' => bcrypt(Str::random(16)),
                'email_verified_at' => now(),
            ]
        );

        // ✅ keep user logged in across tabs
        Auth::login($user, true);

        return redirect()->intended('/dashboard');
    }
}
