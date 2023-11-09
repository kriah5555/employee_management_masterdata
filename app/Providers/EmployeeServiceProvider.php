<?php

namespace App\Providers;

use App\Repositories\Company\LocationRepository;
use App\Repositories\Employee\EmployeeBenefitsRepository;
use App\Repositories\Employee\EmployeeFunctionDetailsRepository;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Repositories\Employee\EmployeeSocialSecretaryDetailsRepository;
use App\Services\User\UserService;
use Illuminate\Support\ServiceProvider;
use App\Services\Employee\EmployeeService;

class EmployeeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EmployeeService::class, function ($app) {
            return new EmployeeService(
                $app->make(EmployeeProfileRepository::class),
                $app->make(EmployeeBenefitsRepository::class),
                $app->make(EmployeeSocialSecretaryDetailsRepository::class),
                $app->make(EmployeeFunctionDetailsRepository::class),
                $app->make(LocationRepository::class),
                $app->make(UserService::class)
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
