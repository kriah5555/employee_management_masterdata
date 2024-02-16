<?php

namespace App\Repositories\Planning;

use App\Models\Company\Company;
use App\Models\Planning\PlanningBase;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\Planning\PlanningRepositoryInterface;

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

    public function getPlansBetweenDates($location_id = '', $workstations = [], $employee_types = [], $startDateOfWeek, $endDateOfWeek, $employee_profile_id = '', $relations = [])
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

    public function getPlans($from_date = '', $to_date = '', $location = '', $workstations = '', $employee_types = [], $employee_id = '', $relations = [], $from_date_time = '', $to_date_time = '')
    {
        $query = PlanningBase::query();

        $query->with($relations);

        if (!empty($location)) {
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
            $from_date_time = date('Y-m-d H:i:s', strtotime($from_date_time));
            $to_date_time = date('Y-m-d H:i:s', strtotime($to_date_time));

            $query->where(function ($query) use ($from_date_time, $to_date_time) { # to get the plans which are overlapping to the given date time
                $query->where('start_date_time', '<=', $from_date_time)
                    ->where('end_date_time', '>=', $to_date_time);
            });
        }

        $query->orderBy('start_date_time');
        $query->orderBy('end_date_time');
        return $query->get();
    }

    public function getPlansByDatesArray($dates_array, $employee_profile_id)
    {
        $query = PlanningBase::query();
        $query->where('employee_profile_id', $employee_profile_id);

        $query->where(function ($query) use ($dates_array) {
            foreach ($dates_array as $date) {
                $startOfDay = date('Y-m-d 00:00:00', strtotime($date));
                $endOfDay = date('Y-m-d 23:59:59', strtotime($date));
                $query->orWhereBetween('start_date_time', [$startOfDay, $endOfDay]);
            }
        });

        return $query->get();
    }

    public function getStartedPlanForEmployee($employee_profile_id, $location_id = '', $ignore_plan_id = '')
    {
        $query = PlanningBase::query();
        if (!empty($location_id)) {
            $query->where('location_id', $location_id);
        }

        if (!empty($ignore_plan_id)) {
            $query->where('id', '!=', $ignore_plan_id);
        }

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

    public function getPlanningsForTimings($employee_profile_id, $date_timings = [])
    {
        $query = PlanningBase::where('employee_profile_id', $employee_profile_id);

        $query->where(function ($query) use ($date_timings) {
            foreach ($date_timings as $date_timing) {
                $start_date_time = date('Y-m-d H:i:s', strtotime($date_timing['start_date_time']));
                $end_date_time = date('Y-m-d H:i:s', strtotime($date_timing['end_date_time']));

                $query->orWhere(function ($query) use ($start_date_time, $end_date_time) {
                    $query->where('start_date_time', $start_date_time)
                        ->where('end_date_time', $end_date_time);
                });
            }
        });

        $query->orderBy('start_date_time');
        $query->orderBy('end_date_time');

        return $query->get();
    }
}
