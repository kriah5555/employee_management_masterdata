<?php

namespace App\Interfaces\Planning;

use App\Models\Planning\PlanningShifts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface PlanningShiftsRepositoryInterface
{
    public function getPlanningShifts($locationId, $workstationId): Collection;

    public function getPlanningShiftById(string $id, array $relations = []): Collection|Builder|PlanningShifts;

    public function deletePlanningShift(PlanningShifts $planning): bool;

    public function createPlanningShift(array $details): PlanningShifts;

    public function updatePlanningShift(PlanningShifts $planning, array $updatedDetails): bool;
}
