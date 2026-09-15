<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     * Fix for MySQL < 5.7.7: "Specified key was too long" with utf8mb4.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
