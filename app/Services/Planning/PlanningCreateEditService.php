<?php

namespace App\Services\Planning;

use App\Interfaces\Planning\PlanningCreateEditInterface;
use App\Models\Planning\PlanningBase;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ModelDeleteFailedException;

class PlanningCreateEditService implements PlanningCreateEditInterface
{
    public function __construct(
        protected PlanningRepository $planningRepository,
        protected EmployeeService $employeeService
    ) {
    }

    public function getEmployeePlanningCreateOptions($values)
    {
        $date = date('Y-m-d', strtotime($values['date']));
        return $this->employeeService->getEmployeeActiveTypesByDate($values['employee_id'], $date);
    }

    public function savePlans($values)
    {
        $currentPids = $plans = [];
        foreach (array_unique($values['dates']) as $date) {
            foreach ($values['timings'] as $timing) {
                $plan = [];
                $startDateTime = date('Y-m-d H:i', strtotime($date . ' ' . $timing['start_time']));
                if (strtotime($timing['start_time']) > strtotime($timing['end_time'])) {
                    $endDateTime = date('Y-m-d H:i', strtotime($date . ' ' . $timing['end_time'] . "+1 day"));
                } else {
                    $endDateTime = date('Y-m-d H:i', strtotime($date . ' ' . $timing['end_time']));
                }
                $plan = [
                    'id'                  => $timing['plan_id'] ?? '',
                    'start_date_time'     => $startDateTime,
                    'end_date_time'       => $endDateTime,
                    'contract_hours'      => europeanToNumeric($timing['contract_hours']),
                    'location_id'         => $values['location_id'],
                    'workstation_id'      => $values['workstation_id'],
                    'function_id'         => $values['function_id'],
                    'employee_type_id'    => $values['employee_type_id'],
                    'employee_profile_id' => $values['employee_id'],
                    'plan_type'           => config('constants.PLAN_TYPE.WEEKLY_PLANNING'),
                ];
                $currentPids[] = $plan['id'];
                $plans[] = $plan;
            }
        }
        $currentPids = array_filter(array_unique($currentPids));
        if (
            $this->PlanHasOverlapWithEachOther($plans)
            || $this->checkPlansOverlap($values['employee_id'], $values['location_id'], $plans, $currentPids)
        ) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => [
                        'Plannings overlapping'
                    ]
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
        DB::connection('tenant')->beginTransaction();
        foreach ($plans as $plan) {
            if ($plan['id'] == '') {
                $this->planningRepository->createPlanning($plan);
            } else {
                $planning = $this->planningRepository->getPlanningById($plan['id']);
                $this->planningRepository->updatePlanning($planning, $plan);
            }
        }
        DB::connection('tenant')->commit();
    }

    public function isOverlap($plan1, $plan2)
    {
        $start1 = strtotime($plan1['start_date_time']);
        $end1 = strtotime($plan1['end_date_time']);
        $start2 = strtotime($plan2['start_date_time']);
        $end2 = strtotime($plan2['end_date_time']);
        return $start1 < $end2 && $end1 > $start2;
    }

    public function PlanHasOverlapWithEachOther($plans)
    {
        $count = count($plans);
        for ($i = 0; $i < $count - 1; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                if ($this->isOverlap($plans[$i], $plans[$j])) {
                    return true;
                }
            }
        }
        return false;
    }

    public function checkPlansOverlap($employeeId, $locationId, $plans, $currentPids)
    {
        return PlanningBase::where('employee_profile_id', $employeeId)
            ->whereNotIn('id', $currentPids)
            ->where(function ($query) use ($plans) {
                foreach ($plans as $plan) {
                    $query->orWhere(function ($q) use ($plan) {
                        $q->where('start_date_time', '<', $plan['end_date_time'])
                            ->where('end_date_time', '>', $plan['start_date_time']);
                    });
                }
            })->exists();
    }

    public function deletePlan(PlanningBase $planning)
    {
        if ($planning->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete planning');
        }

    }
    public function deleteWeekPlans($values)
    {
        $weekDates = getWeekDates($values['week'], $values['year']);
        $startDateOfWeek = reset($weekDates);
        $endDateOfWeek = end($weekDates);
        $plans = $this->planningRepository->getPlansBetweenDates($values['location_id'], [$values['workstation_id']], '', $startDateOfWeek, $endDateOfWeek, $values['employee_id']);
        DB::connection('tenant')->beginTransaction();
        $plans->each(function ($plan) {
            $plan->delete();
        });
        DB::connection('tenant')->commit();
    }
}
