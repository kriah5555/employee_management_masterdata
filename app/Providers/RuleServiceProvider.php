<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Sector\SectorService;
use App\Services\Sector\SectorSalaryService;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Interfaces\RuleRepositoryInterface;
use App\Services\Rule\RuleService;

class RuleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->bind(RuleService::class, function ($app) {
            return new RuleService($app->make(RuleRepositoryInterface::class));
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
