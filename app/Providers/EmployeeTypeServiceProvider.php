<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmployeeTypeService;

class EmployeeTypeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeTypeService::class, function () {
            return new EmployeeTypeService();
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
