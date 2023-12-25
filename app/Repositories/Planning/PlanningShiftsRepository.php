<?php

namespace App\Repositories\Planning;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\Planning\PlanningShiftsRepositoryInterface;
use App\Models\Planning\PlanningShifts;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class PlanningShiftsRepository implements PlanningShiftsRepositoryInterface
{
    public function getPlanningShifts($locationId, $workstationId): Collection
    {
        return PlanningShifts::where('location_id', $locationId)->where('workstation_id', $workstationId)->get();
    }
    public function storePlanningShifts($data)
    {
        $shiftIds = [];
        foreach ($data['shifts'] as $shiftData) {
            $shiftObj = PlanningShifts::firstOrCreate(
                [
                    'start_time'     => $shiftData['start_time'],
                    'end_time'       => $shiftData['end_time'],
                    'contract_hours' => europeanToNumeric($shiftData['contract_hours']),
                    'location_id'    => $data['location_id'],
                    'workstation_id' => $data['workstation_id'],
                ]
            );
            $shiftIds[] = $shiftObj->id;
        }
        PlanningShifts::where('location_id', $data['location_id'])
            ->where('workstation_id', $data['workstation_id'])
            ->whereNotIn('id', $shiftIds)
            ->delete();
    }

    public function getPlanningShiftById(string $id, array $relations = []): Collection|Builder|PlanningShifts
    {
        return PlanningShifts::with($relations)->findOrFail($id);
    }

    public function deletePlanningShift(PlanningShifts $planningShift): bool
    {
        if ($planningShift->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete planning');
        }
    }

    public function createPlanningShift(array $details): PlanningShifts
    {
        return PlanningShifts::create($details);
    }

    public function updatePlanningShift(PlanningShifts $planningShift, array $updatedDetails): bool
    {
        if ($planningShift->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update planning');
        }
    }
}
