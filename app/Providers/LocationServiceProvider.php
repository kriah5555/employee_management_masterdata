<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Company\LocationService;
use App\Services\Sector\SectorService;
use App\Models\Company\Location;

class LocationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(LocationService::class, function ($app) {
            return new LocationService(
                $app->make(Location::class),
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
