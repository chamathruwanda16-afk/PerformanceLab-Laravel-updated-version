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
    return Socialite::driver('google')->redirect();
}

public function callback()
{
    $googleUser = Socialite::driver('google')->user();

    $user = User::updateOrCreate(
        ['email' => $googleUser->getEmail()],
        [
            'name' => $googleUser->getName() ?? 'Google User',
            'password' => bcrypt(Str::random(16)),
            'email_verified_at' => now(),
        ]
    );

    Auth::login($user, true);

    return redirect()->route('dashboard');
}

}
