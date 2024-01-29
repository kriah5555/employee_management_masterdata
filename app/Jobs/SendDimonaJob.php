<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Dimona\DimonaSenderService;

class SendDimonaJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $companyId;
    protected $timeRegistraionId;
    public function __construct($companyId, $timeRegistraionId)
    {
        $this->companyId = $companyId;
        $this->timeRegistraionId = $timeRegistraionId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            app(DimonaSenderService::class)->sendDimona($this->companyId, $this->timeRegistraionId);
        } catch (\Exception $e) {
            \Log::error('Error processing job: ' . $e->getMessage());
            throw $e; // Rethrow the exception to mark the job as failed
        }
    }
}
