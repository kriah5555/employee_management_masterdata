<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\EmployeeFunction\FunctionService;
use App\Repositories\EmployeeFunction\FunctionCategoryRepository;
use App\Repositories\EmployeeFunction\FunctionTitleRepository;

class FunctionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(FunctionService::class, function ($app) {
            return new FunctionService(
                $app->make(FunctionCategoryRepository::class),
                $app->make(FunctionTitleRepository::class)
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