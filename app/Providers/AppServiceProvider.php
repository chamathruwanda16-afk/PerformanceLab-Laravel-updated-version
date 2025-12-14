<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\SearchLog;
use App\Models\ProductView;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
      
    }

   
    public function boot(): void
    {
        //
    }

}
