<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TwoFactorAuthController extends Controller
{
    public function enable(Request $request)
    {
        if (! $request->user()->two_factor_secret) {
            $request->user()->enableTwoFactorAuthentication();
        }

        return back()->with('success', 'Two-factor authentication enabled.');
    }

    public function disable(Request $request)
    {
        $request->user()->disableTwoFactorAuthentication();

        return back()->with('success', 'Two-factor authentication disabled.');
    }

    public function regenerateRecoveryCodes(Request $request)
    {
        $request->user()->generateNewRecoveryCodes();

        return back()->with('success', 'Recovery codes regenerated.');
    }

}
