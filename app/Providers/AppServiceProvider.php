<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;   // ✅ add this
use App\Models\SearchLog;
use App\Models\ProductView;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // ✅ Force https links in production (fixes mixed content on Render)
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }
}
