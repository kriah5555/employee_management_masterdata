<?php

namespace App\Services\Planning;

use App\Services\Employee\EmployeeContractService;
use App\Services\Planning\PlanningService;
use App\Models\Planning\LongTermPlanning;
use App\Models\Planning\LongTermPlanningTimings;
use Illuminate\Support\Facades\DB;
use DateTime;
use App\Models\Planning\PlanningBase;


class LongTermPlanningService
{

    public function __construct(
        protected PlanningService $planningService,
        protected EmployeeContractService $employeeContractService,
    ) {

    }

    public function storeLongTermPlanning($values)
    {
        $values['start_date'] = date('Y-m-d', strtotime($values['start_date']));
        $values['end_date'] = array_key_exists('end_date', $values) ? date('Y-m-d', strtotime($values['end_date'])) : date('Y-m-d', strtotime($values['start_date'] . '+1 year'));
        DB::connection('master')->beginTransaction();
        $longTermPlanning = LongTermPlanning::create([
            'employee_profile_id' => $values['employee_id'],
            'function_id'         => $values['function_id'],
            'workstation_id'      => $values['workstation_id'],
            'location_id'         => $values['location_id'],
            'start_date'          => $values['start_date'],
            'end_date'            => $values['end_date'],
            'repeating_week'      => $values['repeating_week'],
            'auto_renew'          => $values['auto_renew'],
        ]);
        foreach ($values['plannings'] as $key => $weekPlanning) {
            foreach ($weekPlanning as $planning) {
                LongTermPlanningTimings::create([
                    'long_term_planning_id' => $longTermPlanning->id,
                    'day'                   => $planning['day'],
                    'start_time'            => $planning['start_time'],
                    'end_time'              => $planning['end_time'],
                    'contract_hours'        => europeanToNumeric($planning['contract_hours']),
                    'week_no'               => $key + 1,
                ]);
            }
        }
        $this->createPlanningsForLongTerm($longTermPlanning);
        DB::connection('master')->commit();
    }

    public function createPlanningsForLongTerm($longTermPlanning)
    {
        $plans = [];
        foreach ($longTermPlanning->longTermPlanningTimings as $planning) {
            $dates = $this->getDatesForLongTermPlanning($longTermPlanning->start_date, $longTermPlanning->end_date, $longTermPlanning->repeating_week, $planning->day, $planning->week_no);
            foreach ($dates as $date) {
                $startDateTime = date('Y-m-d H:i', strtotime($date . ' ' . $planning->start_time));
                if (strtotime($planning->start_time) > strtotime($planning->end_time)) {
                    $endDateTime = date('Y-m-d H:i', strtotime($date . ' ' . $planning->end_time . "+1 day"));
                } else {
                    $endDateTime = date('Y-m-d H:i', strtotime($date . ' ' . $planning->end_time));
                }
                $contract = $this->employeeContractService->checkContractExistForLongTermPlanning($longTermPlanning->employee_profile_id, $longTermPlanning->start_date, $longTermPlanning->end_date);
                $plans[] = [
                    'start_date_time'     => $startDateTime,
                    'end_date_time'       => $endDateTime,
                    'contract_hours'      => $planning->contract_hours,
                    'location_id'         => $longTermPlanning->location_id,
                    'workstation_id'      => $longTermPlanning->workstation_id,
                    'function_id'         => $longTermPlanning->function_id,
                    'employee_type_id'    => $contract->employee_type_id,
                    'employee_profile_id' => $longTermPlanning->employee_profile_id,
                    'plan_type'           => config('constants.PLAN_TYPE.OTH_PLANNING'),
                ];
            }
        }
        PlanningBase::insert($plans);
    }

    public function getDatesForLongTermPlanning($startDate, $endDate, $repeatingWeeks, $dayOfWeek, $planWeekNo)
    {
        if (strtotime($startDate) <= strtotime(date('Y-m-d'))) {
            $startDate = date('Y-m-d', strtotime(date('Y-m-d') . '+1 day'));
        }
        $result = [];
        $currentDate = new DateTime($startDate);
        $endDateTime = new DateTime($endDate);
        while ((int) $currentDate->format('N') != $dayOfWeek) {
            $currentDate->modify('+1 day');
        }
        $increment = ($planWeekNo - 1) * 7;
        $currentDate->modify('+' . $increment . ' day');
        while ($currentDate <= $endDateTime) {
            $result[] = $currentDate->format('Y-m-d');
            $increment = $repeatingWeeks * 7;
            $currentDate->modify('+' . $increment . ' day');
        }
        return $result;
    }

    public function getEmployeeLongTermPlannings($employeeProfileId)
    {
        $longTermPlannings = LongTermPlanning::with(['longTermPlanningTimings', 'location', 'workstation', 'functionTitle'])
            ->where('employee_profile_id', $employeeProfileId)
            ->get();
        return $this->formatEmployeeLongTermPlannings($longTermPlannings);
    }

    public function formatEmployeeLongTermPlannings($longTermPlannings)
    {
        $response = [];
        foreach ($longTermPlannings as $longTermPlanning) {
            $response[] = $this->formatLongTermPlanning($longTermPlanning);
        }
        return $response;
    }

    public function formatLongTermPlanning($longTermPlanning)
    {
        $plannings = [];
        foreach ($longTermPlanning->longTermPlanningTimings as $longTermPlanningTiming) {
            $plannings[$longTermPlanningTiming->week_no][] = [
                'id'             => $longTermPlanningTiming->id,
                'start_time'     => date('H:i', strtotime($longTermPlanningTiming->start_time)),
                'end_time'       => date('H:i', strtotime($longTermPlanningTiming->end_time)),
                'day'            => $longTermPlanningTiming->day,
                'contract_hours' => numericToEuropean($longTermPlanningTiming->contract_hours),
                'week_no'        => $longTermPlanningTiming->week_no,
            ];
        }
        return [
            'id'             => $longTermPlanning->id,
            'start_date'     => date('d-m-Y', strtotime($longTermPlanning->start_date)),
            'end_date'       => date('d-m-Y', strtotime($longTermPlanning->end_date)),
            'repeating_week' => $longTermPlanning->repeating_week,
            'auto_renew'     => $longTermPlanning->auto_renew,
            'function'       => [
                'value' => $longTermPlanning->functionTitle->id,
                'label' => $longTermPlanning->functionTitle->name,
            ],
            'workstation'    => [
                'value' => $longTermPlanning->workstation->id,
                'label' => $longTermPlanning->workstation->workstation_name,
            ],
            'location'       => [
                'value' => $longTermPlanning->location->id,
                'label' => $longTermPlanning->location->location_name,
            ],
            'plannings'      => array_values($plannings)
        ];
    }

    public function getLongTermPlanningsByDate($employeeProfileId, $locationId, $startDate, $endDate)
    {
        return LongTermPlanning::where('employee_profile_id', $employeeProfileId)
            ->where('location_id', $locationId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where(function ($query) use ($startDate) {
                    $query->where('start_date', '<=', $startDate)
                        ->where('end_date', '>=', $startDate);
                })->orWhere(function ($query) use ($endDate) {
                    $query->where('start_date', '<=', $endDate)
                        ->where('end_date', '>=', $endDate);
                })->orWhere(function ($query) use ($startDate, $endDate) {
                    $query->where('start_date', '>=', $startDate)
                        ->where('end_date', '<=', $endDate);
                });
            })
            ->get();
    }


    public function getEmployeeRenewingLongTermPlanning($employeeProfileId, $locationId)
    {
        return LongTermPlanning::where('employee_profile_id', $employeeProfileId)
            ->where('location_id', $locationId)
            ->where('auto_renew', true)
            ->get();
    }

    public function deleteLongTermPlanning($longTermPlanningId)
    {
        DB::connection('master')->beginTransaction();
        $longTermPlanning = $this->getLongTermPlanningById($longTermPlanningId);
        $this->deletePlanningsForLongTermPlanning($longTermPlanning);
        $longTermPlanning->delete();
        DB::connection('master')->commit();
    }

    public function deletePlanningsForLongTermPlanning($longTermPlanning)
    {
        $endDate = date('Y-m-d 23:59:59', strtotime($longTermPlanning->end_date));
        if (strtotime($longTermPlanning->start_date) > strtotime(date('Y-m-d'))) {
            $startDate = date('Y-m-d 00:00:00', strtotime($longTermPlanning->start_date));
        } else {
            $startDate = date('Y-m-d 23:59:59', strtotime(date('Y-m-d') . '+1 day'));
            if (strtotime($startDate) > strtotime($endDate)) {
                return;
            }
        }
        PlanningBase::where('employee_profile_id', $longTermPlanning->employee_profile_id)
            ->where('location_id', $longTermPlanning->location_id)
            ->where('start_date_time', '>=', $startDate)
            ->where('end_date_time', '<=', $endDate)
            ->delete();
    }
    public function getLongTermPlanningDetails($longTermPlanningId)
    {
        $longTermPlanning = $this->getLongTermPlanningById($longTermPlanningId);
        return $this->formatLongTermPlanning($longTermPlanning);
    }

    public function updateLongTermPlanning($longTermPlanningId, $values)
    {
        $longTermPlanning = $this->getLongTermPlanningById($longTermPlanningId);
        dd([$longTermPlanning, $values]);
    }

    public function getLongTermPlanningById(string $longTermPlanningId, array $relations = [])
    {
        return LongTermPlanning::with($relations)->findOrFail($longTermPlanningId);
    }
}
