<?php

namespace App\Services\Planning;

use App\Services\Planning\PlanningService;
use App\Models\Planning\LongTermPlanning;
use App\Models\Planning\LongTermPlanningTimings;
use Illuminate\Support\Facades\DB;


class LongTermPlanningService
{

    public function __construct(
        protected PlanningService $planningService,
    ) {

    }

    public function storeLongTermPlannings($values)
    {
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
        DB::connection('tenant')->commit();
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
            $plannings = [];
            foreach ($longTermPlanning->longTermPlanningTimings as $longTermPlanningTiming) {
                $plannings[] = [
                    'id'             => $longTermPlanningTiming->id,
                    'start_time'     => date('H:i', strtotime($longTermPlanningTiming->start_time)),
                    'end_time'       => date('H:i', strtotime($longTermPlanningTiming->end_time)),
                    'day'            => $longTermPlanningTiming->day,
                    'contract_hours' => numericToEuropean($longTermPlanningTiming->contract_hours),
                ];
            }
            $longTermPlanningDetails = [
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
                'plannings'      => $plannings
            ];
            $response[] = $longTermPlanningDetails;
        }
        return $response;
    }

}
