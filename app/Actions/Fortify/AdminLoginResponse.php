<?php

namespace App\Actions\Fortify;

use Laravel\Fortify\Contracts\LoginResponse;

class AdminLoginResponse implements LoginResponse
{
    public function toResponse($request)
    {
        $user = $request->user();

        $target = ($user && ($user->is_admin ?? false))
            ? route('admin.dashboard')
            : route('account.index'); // or route('dashboard') if you prefer

        return redirect()->intended($target);
    }
}
