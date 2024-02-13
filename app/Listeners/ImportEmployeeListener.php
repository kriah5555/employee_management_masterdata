<?php

namespace App\Listeners;

use App\Events\ImportEmployeeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ImportEmployeeListener extends ShouldQueue
{
    public function __construct()
    {
        //
    }

    public function handle(ImportEmployeeEvent $event): void
    {
        $importEmployee = $event->importEmployee;

    }
}
