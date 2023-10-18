<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReasonService;
use App\Repositories\ReasonRepository;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ReasonService::class, function ($app) {
            return new ReasonService(
                $app->make(ReasonRepository::class),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
    }

}