<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class MailScheduler extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:mail';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The scheduler for sending the mails';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       Artisan::call('queue:work --queue=mails_queue --stop-when-empty --max-time=60');
       $this->info(date('d-m-Y H:i:s') . ' => ' . 'Mails queue.');
    }
}
