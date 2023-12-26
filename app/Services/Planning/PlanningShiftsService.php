<?php

namespace App\Services\Planning;

use App\Repositories\Planning\PlanningShiftsRepository;
use Illuminate\Support\Facades\DB;


class PlanningShiftsService
{

    public function __construct(
        protected PlanningShiftsRepository $planningShiftsRepository
    ) {
    }

    public function getPlanningShifts($locationId, $workstationId)
    {
        return $this->planningShiftsRepository->getPlanningShifts($locationId, $workstationId);
    }
    public function storePlanningShifts($values)
    {
        return DB::transaction(function () use ($values) {
            return $this->planningShiftsRepository->storePlanningShifts($values);
        });
    }
}
