<?php

namespace App\Providers;

use App\Models\Barang;
use App\Models\User;
use App\Observers\BarangObserver;
use App\Observers\UserObserver;
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
        Barang::observe(BarangObserver::class);
        User::observe(UserObserver::class);
    }
}
