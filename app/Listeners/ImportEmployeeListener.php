<?php

namespace App\Listeners;

use App\Events\ImportEmployeeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Services\Employee\ImportEmployeeService;

class ImportEmployeeListener
{
    public function __construct()
    {
        //
    }

    public function handle(ImportEmployeeEvent $event): void
    {
        try {
            app(ImportEmployeeService::class)->importEmployee($event->importEmployee);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
