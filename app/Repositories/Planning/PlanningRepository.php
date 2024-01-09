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

    public function getPlansBetweenDates($location_id = '', $workstations = [], $employee_types = '', $startDateOfWeek, $endDateOfWeek, $employee_profile_id = '', $relations = [])
    {
        $startDateOfWeek = date('Y-m-d 00:00:00', strtotime($startDateOfWeek));
        $endDateOfWeek = date('Y-m-d 23:59:59', strtotime($endDateOfWeek));
        $query = PlanningBase::query();
        $query->with($relations);

        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        }

        if (!empty($workstations)) {
            $query->whereIn('workstation_id', $workstations);
        }

        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', $employee_types);
        }

        if (!empty($employee_profile_id)) {
            $query->where('employee_profile_id', $employee_profile_id);
        }

        $query->whereBetween('start_date_time', [$startDateOfWeek, $endDateOfWeek]);
        $query->orderBy('start_date_time');
        $query->orderBy('end_date_time');
        return $query->get();
    }

    public function getPlans($from_date = '', $to_date = '', $location = '', $workstations = '', $employee_types = '', $employee_id = '', $relations = [], $from_date_time = '', $to_date_time = '')
    {
        $query = PlanningBase::query();

        $query->with($relations);

        if (!empty($workstations)) {
            $query->where('location_id', $location);
        }

        if (!empty($workstations)) {
            $query->whereIn('workstation_id', $workstations);
        }

        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', $employee_types);
        }

        if (!empty($employee_id)) {
            $query->where('employee_profile_id', $employee_id);
        }

        if (!empty($from_date) && !empty($to_date)) {
            $from_date = date('Y-m-d 00:00:00', strtotime($from_date));
            $to_date = date('Y-m-d 23:59:59', strtotime($to_date));
            $query->whereBetween('start_date_time', [$from_date, $to_date]);
        } elseif (!empty($from_date_time) && !empty($to_date_time)) {
            $query->where(function ($query) use ($from_date_time, $to_date_time) { # to get the plans which are overlapping to the given date time
                $query->where('start_date_time', '<=', $from_date_time)
                    ->where('end_date_time', '>=', $to_date_time);
            });
        }

        $query->orderBy('start_date_time');
        $query->orderBy('end_date_time');
        return $query->get();
    }

    public function getStartedPlanForEmployee($employee_profile_id, $location_id)
    {
        $query = PlanningBase::query();
        $query->where('location_id', $location_id);
        $query->where('employee_profile_id', $employee_profile_id);
        $query->where('plan_started', true);
        $query->orderBy('start_date_time');
        $query->orderBy('end_date_time');
        return $query->get();
    }

    public function getMonthlyPlanningDayCount($location, $workstations, $employee_types, $startDateOfMonth, $endDateOfMonth)
    {
        $startDateOfMonth = date('Y-m-d 00:00:00', strtotime($startDateOfMonth));
        $endDateOfMonth = date('Y-m-d 23:59:59', strtotime($endDateOfMonth));
        $query = PlanningBase::where('location_id', $location);
        if (!empty($workstations)) {
            $query->whereIn('workstation_id', $workstations);
        }
        if (!empty($employee_types)) {
            $query->whereIn('employee_type_id', $employee_types);
        }
        $query->whereBetween('start_date_time', [$startDateOfMonth, $endDateOfMonth]);
        $query->selectRaw('DATE(start_date_time) as date, COUNT(*) as count')->groupBy('date');
        return $query->get();
    }
}
