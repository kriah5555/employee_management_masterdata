<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Repositories\EmployeeType\EmployeeTypeRepository;
use App\Repositories\EmployeeType\EmployeeTypeConfigRepository;
use App\Repositories\EmployeeType\EmployeeTypeDimonaConfigRepository;
use App\Repositories\EmployeeType\EmployeeTypeCategoryRepository;

class EmployeeTypeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeTypeService::class, function ($app) {
            return new EmployeeTypeService(
                $app->make(EmployeeTypeRepository::class),
                $app->make(EmployeeTypeConfigRepository::class),
                $app->make(EmployeeTypeDimonaConfigRepository::class),
                $app->make(EmployeeTypeCategoryRepository::class)
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