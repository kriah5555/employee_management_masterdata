<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\ReasonService;
use App\Repositories\ReasonRepository;
use App\Services\Email\MailService;
use App\Services\Email\EmailTemplateService;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Repositories\Company\CompanyRepository;

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
        $this->app->singleton(MailService::class, function ($app) {
            return new MailService(
                $app->make(EmailTemplateService::class),
                $app->make(EmployeeProfileRepository::class),
                $app->make(CompanyRepository::class),
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
