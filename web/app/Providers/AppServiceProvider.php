<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

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
     */
    public function boot(): void
    {
        // ponytail: 1 baris ini menggantikan "komponen pagination" —
        // semua ->links() site-wide otomatis pakai gaya daisy join
        Paginator::defaultView('vendor.pagination.archery');
        Paginator::defaultSimpleView('vendor.pagination.archery');
    }
}
