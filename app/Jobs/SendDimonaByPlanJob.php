<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\Dimona\DimonaSenderService;

class SendDimonaByPlanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    protected $companyId;
    protected $planningId;
    public function __construct($companyId, $planningId)
    {
        $this->companyId = $companyId;
        $this->planningId = $planningId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            app(DimonaSenderService::class)->sendDimonaByPlan($this->companyId, $this->planningId);
        } catch (\Exception $e) {
            \Log::error('Error processing job: ' . $e->getMessage());
            throw $e; // Rethrow the exception to mark the job as failed
        }
    }
}
