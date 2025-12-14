<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        // Basic validation
        $credentials = $request->validate([
            'email'    => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt login
        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        $user = $request->user();

        // Admins => /admin, others => /account
        $target = ($user->is_admin ?? false)
            ? route('admin.dashboard')
            : route('account.index');

        return redirect()->intended($target);
    }
}
