<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Sector\SectorService;
use App\Services\Sector\SectorSalaryService;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Repositories\Sector\SectorRepository;
use App\Repositories\Sector\SectorSalaryConfigRepository;

class SectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(SectorService::class, function ($app) {
            return new SectorService(
                $app->make(SectorRepository::class),
                $app->make(SectorSalaryConfigRepository::class),
                $app->make(EmployeeTypeService::class)
            );
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