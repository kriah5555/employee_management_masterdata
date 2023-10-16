<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CompanyService;
use App\Services\LocationService;
use App\Services\Sector\SectorService;
use App\Models\Company;

class CompanyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CompanyService::class, function ($app) {
            return new CompanyService(
                $app->make(Company::class),
                $app->make(SectorService::class),
                $app->make(LocationService::class)
            );
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