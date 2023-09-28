<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Sector\SectorService;
use App\Services\Sector\SectorSalaryService;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\EmployeeFunction\FunctionService;

class SectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(SectorService::class, function ($app) {
            return new SectorService($app->make(EmployeeTypeService::class));
            // return new SectorService(
            //     $app->make(EmployeeTypeService::class),
            //     $app->make(FunctionService::class)
            // );
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