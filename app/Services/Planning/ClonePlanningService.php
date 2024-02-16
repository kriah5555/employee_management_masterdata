<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Repositories\Planning\PlanningRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;

class ClonePlanningService
{

    protected $planningRepository;
    public function __construct(PlanningRepository $planningRepository)
    {
        $this->planningRepository = $planningRepository;
    }
    public function clonePlanning($details)
    {
        $fromYear = $details->from_year;
        $toYear = $details->to_year;
        $fromWeeks = $details->from_week;
        $toWeeks = $details->to_week;
        $employeeTypeIds = $details->employee_types;
        $employeeProfileId = $details->employee_names;
        $locationId = $details->location_id;

        $overlappingErrors = $this->checkForOverlappingPlans($fromWeeks, $fromYear, $toWeeks, $toYear, $employeeTypeIds, $employeeProfileId, $locationId);

        if (!empty($overlappingErrors)) {
            return $overlappingErrors;
        } else {

            $newPlansData = $this->generateNewPlans($fromWeeks, $fromYear, $toWeeks, $toYear, $employeeTypeIds, $employeeProfileId, $locationId);
            //inserting all data
            $result = DB::table('planning_base')->insert(
                $newPlansData
            );
            $message = ['plans cloned successfully'];
            return $result ? $message : $result;
        }
    }

    public function fetchPlans(array $weeks, int $year, array $employeeTypeIds, array $employeeProfileId = [], $locationId)
    {
        $fetchedPlans = [];
        $datesAndWeeks = [];
        foreach ($weeks as $week) {
            $dates = getWeekDates($week, $year);
            $datesAndWeeks[] = [
                'week' => $week,
                'dates' => $dates,
            ];
        }
        foreach ($datesAndWeeks as  $weekWithDates) {
            foreach ($weekWithDates['dates'] as $date) {
                $plansQuery = PlanningBase::whereYear('start_date_time', $year)
                    ->where('location_id', $locationId)
                    ->whereIn('employee_profile_id', $employeeProfileId)
                    ->whereDate('start_date_time', $date);
                if (!empty($employeeTypeIds)) {
                    $plansQuery->whereIn('employee_type_id', $employeeTypeIds);
                }
                $plans = $plansQuery->get();
                $fetchedPlans[] =  [
                    'week' => $weekWithDates['week'],
                    'date' => $date,
                    'plans' => $plans
                ];
            }
        }
        return $fetchedPlans;
    }

    public function planOverlap($plan1, $plan2)
    {
        //  Convert start and end times to DateTime objects Extract time parts from the datetime strings
        $startTime1 = date('H:i:s', strtotime($plan1['start_date_time']));
        $endTime1 = date('H:i:s', strtotime($plan1['end_date_time']));
        $startTime2 = date('H:i:s', strtotime($plan2['start_date_time']));
        $endTime2 = date('H:i:s', strtotime($plan2['end_date_time']));

        // Check if the employee profile ID matches
        $employeeProfileIdMatches = $plan1['employee_profile_id'] === $plan2['employee_profile_id'];

        // Check for overlap
        return $employeeProfileIdMatches && $startTime1 < $endTime2 && $endTime1 > $startTime2;
    }

    private function checkForOverlappingPlans($fromWeeks, $fromYear, $toWeeks, $toYear, $employeeTypeIds, $employeeProfileId, $locationId)
    {

        $pastPlans = $this->fetchPlans($fromWeeks, $fromYear, $employeeTypeIds, $employeeProfileId, $locationId);
        //passing empty array to fetch all the employee types data
        $futurePlans = $this->fetchPlans($toWeeks, $toYear, [], $employeeProfileId, $locationId);

        $overlappingErrors = [];
        foreach ($pastPlans as $index => $pastPlansForEachDay) {
            $plans = $futurePlans[$index]['plans'];

            if (!empty($plans)) {
                foreach ($plans as $plan) {
                    foreach ($pastPlansForEachDay['plans'] as $pPlan) {
                        if ($this->planOverlap($plan, $pPlan)) {
                            $overlappingErrors[$plan->plan_date . '/' . $pPlan->plan_date][] = $plan->employeeProfile->full_name;
                        }
                    }
                }
            }
        }

        return $this->formatOverlappingErrors($overlappingErrors);
    }

    private function formatOverlappingErrors($overlappingErrors)
    {
        $errors = [];
        foreach ($overlappingErrors as $date => $employee_names) {
            $errors[] = $date . ' : planans are overlapping for employees -> ' . implode('; ', array_unique($employee_names));
        }
        return $errors;
    }

    private function generateNewPlans($fromWeeks, $fromYear, $toWeeks, $toYear, $employeeTypeIds, $employeeProfileId, $locationId)
    {

        $pastPlans = $this->fetchPlans($fromWeeks, $fromYear, $employeeTypeIds, $employeeProfileId, $locationId);
        $futurePlans = $this->fetchPlans($toWeeks, $toYear, [], $employeeProfileId, $locationId);

        $newPlans = [];

        foreach ($pastPlans as $index => $plansForDay) {
            foreach ($plansForDay['plans'] as $plan) {
                $newPlans[] = [
                    'start_date_time'     =>  date('Y-m-d H:i', strtotime($futurePlans[$index]['date'] . ' ' . date('H:i:s', strtotime($plan['start_date_time'])))),
                    'end_date_time'       =>  date('Y-m-d H:i', strtotime($futurePlans[$index]['date'] . ' ' . date('H:i:s', strtotime($plan['end_date_time'])))),
                    'contract_hours'      => $plan->contract_hours,
                    'location_id'         =>  $plan->location_id,
                    'workstation_id'      =>  $plan->workstation_id,
                    'function_id'         =>  $plan->function_id,
                    'employee_type_id'    =>  $plan->employee_type_id,
                    'employee_profile_id' =>  $plan->employee_profile_id,
                    'plan_type'           =>  $plan->plan_type,
                ];
            }
        }

        return $newPlans;
    }
}
