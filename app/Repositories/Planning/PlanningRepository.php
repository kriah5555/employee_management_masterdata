<?php

namespace App\Repositories\Planning;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\Planning\PlanningRepositoryInterface;
use App\Models\Planning\PlanningBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Company\Company;

class PlanningRepository implements PlanningRepositoryInterface
{
    public function getPlannings(): Collection
    {
        return PlanningBase::all();
    }

    public function getPlanningById(string $id, array $relations = []): Collection|Builder|PlanningBase
    {
        return PlanningBase::with($relations)->findOrFail($id);
    }

    public function deletePlanning(PlanningBase $planning): bool
    {
        if ($planning->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete planning');
        }
    }

    public function createPlanning(array $details): PlanningBase
    {
        return PlanningBase::create($details);
    }

    public function updatePlanning(PlanningBase $planning, array $updatedDetails): bool
    {
        if ($planning->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update planning');
        }
    }

    public function getPlansBetweenDates($location, $workstations, $employee_types, $startDateOfWeek, $endDateOfWeek, $relations = [])
    {
        $startDateOfWeek = date('Y-m-d 00:00:00', strtotime($startDateOfWeek));
        $endDateOfWeek = date('Y-m-d 23:59:59', strtotime($endDateOfWeek));
        $query = PlanningBase::where('location_id', $location);
        $query->with($relations);
        if (!empty($workstations)) {
            $query->whereIn('workstation_id', $workstations);
        }
        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', $employee_types);
        }
        $query->whereBetween('start_date_time', [$startDateOfWeek, $endDateOfWeek]);
        return $query->get();
    }
}
