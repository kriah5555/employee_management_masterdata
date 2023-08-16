<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SectorService;
use App\Services\SectorSalaryService;

class SectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(SectorService::class, function () {
            return new SectorService();
        });
        $this->app->bind(SectorSalaryService::class, function ($app) {
            return new SectorSalaryService($app->make(SectorService::class));
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
