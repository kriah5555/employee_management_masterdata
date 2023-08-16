<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FunctionService;
use App\Services\SectorService;

class FunctionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FunctionService::class, function ($app) {
            return new FunctionService($app->make(SectorService::class));
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
