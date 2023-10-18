<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Contract\ContractTypeService;
use App\Repositories\Contract\ContractTypeRepository;
use App\Services\Contract\ContractRenewalTypeService;
use App\Repositories\Contract\ContractRenewalTypeRepository;

class ContractServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(ContractTypeService::class, function ($app) {
            return new ContractTypeService(
                $app->make(ContractTypeRepository::class)
            );
        });
        $this->app->bind(ContractRenewalTypeService::class, function ($app) {
            return new ContractRenewalTypeService(
                $app->make(ContractRenewalTypeRepository::class)
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