<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Holiday\HolidayService;
use App\Repositories\Holiday\HolidayCodeRepository;

class HolidayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(HolidayService::class, function ($app) {
            return new HolidayService(
                $app->make(HolidayCodeRepository::class),
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