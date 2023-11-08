<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CompanyService;
use App\Services\AddressService;
use App\Repositories\Company\CompanyRepository;
use App\Services\Company\LocationService;
use App\Services\WorkstationService;
use App\Repositories\Company\CompanySocialSecretaryDetailsRepository;

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
                $app->make(CompanySocialSecretaryDetailsRepository::class),
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
