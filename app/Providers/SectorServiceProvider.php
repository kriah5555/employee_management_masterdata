<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SectorService;

class SectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(SectorService::class, function ($app) {
            return new SectorService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
