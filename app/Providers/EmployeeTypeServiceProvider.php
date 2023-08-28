<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\Contract\ContractTypeService;

class EmployeeTypeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeTypeService::class, function ($app) {
            return new EmployeeTypeService($app->make(ContractTypeService::class));
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
