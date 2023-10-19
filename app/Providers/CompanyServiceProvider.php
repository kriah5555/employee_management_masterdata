<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CompanyService;
use App\Services\AddressService;
use App\Repositories\Company\CompanyRepository;
use App\Services\LocationService;
use App\Services\WorkstationService;

class CompanyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(CompanyService::class, function ($app) {
            return new CompanyService(
                $app->make(CompanyRepository::class),
                $app->make(LocationService::class),
                $app->make(AddressService::class),
                $app->make(WorkstationService::class),
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