<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Company\CompanyService;
use App\Services\Company\DashboardAccessService;
use App\Services\WorkstationService;
use App\Services\EmployeeFunction\FunctionService;
use App\Models\Company\Workstation;
use App\Models\Company\Location;
use App\Models\Company\Company;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Services\Employee\EmployeeService;
use App\Services\Planning\PlanningContractService;


class UurroosterService implements PlanningInterface
{

    public function __construct(
        protected DashboardAccessService $dashboardAccessService,
        protected PlanningBase $planningBase,
        protected CompanyService $companyService,
        protected PlanningRepository $planningRepository
    ) {
    }
    public function getUurroosterData($values)
    {
        $date = date('Y-m-d', strtotime($values['date']));
        if ($values['location_id']) {
            $qr_token = [
                'company_id'  => getCompanyId(),
                'location_id' => $values['location_id'],
            ];
            $qr_token = encodeData($qr_token);
            //Week dates.
            $workstationsRaw = Location::find($values['location_id'])->with('workstations')->first();
            $response = [
                'qr_token'      => $qr_token,
                'location_name' => $workstationsRaw->location_name,
                'planning_data' => [],
            ];
            foreach ($workstationsRaw['workstations'] as $value) {
                $response['planning_data'][$value->id]['workstation_id'] = $value->workstation_name;
                $response['planning_data'][$value->id]['workstation_name'] = $value->workstation_name;
                $response['planning_data'][$value->id]['count'] = 0;
                $response['planning_data'][$value->id]['plannings'] = [];
            }
            $data = $this->planningRepository->getPlansBetweenDates($values['location_id'], [], [], $date, $date, '', ['workStation', 'employeeProfile.user', 'employeeType', 'functionTitle', 'timeRegistrations']);
            return $this->formatUurroosterData($data, $response);
            // } else {
            //     $tokenData = $this->dashboardAccessService->decodeDashboardToken($dashboardToken);
        }
        // if (empty($tokenData)) {

        // } else {
        //     $companyId = $tokenData['company_id'];
        //     $locationId = $tokenData['location_id'];
        // }
    }
    public function formatUurroosterData($plannings, $response)
    {
        foreach ($plannings as $planning) {
            $employeeName = $planning->employeeProfile->user->userBasicDetails->first_name . ' ' . $planning->employeeProfile->user->userBasicDetails->last_name;
            $timeRegistrations = [
                'start_time'          => [],
                'start_dimona_status' => [],
                'end_time'            => [],
                'end_dimona_status'   => [],
            ];
            if (count($planning->timeRegistrations)) {
                $count = 0;
            } else {
                $count = 1;
            }
            $messages = [
                [
                    'status'  => 'success',
                    'message' => ['Dimona is successful']
                ],
                [
                    'status'  => 'warning',
                    'message' => ['Dimona is pending']
                ],
                [
                    'status'  => 'failed',
                    'message' => ['0000-000:Failed to send dimona']
                ],
                [
                    'status'  => 'failed',
                    'message' => [
                        '0777-555:Double dimona period',
                        '0777-005:Invalid dimona'
                    ]
                ],
            ];
            foreach ($planning->timeRegistrations as $timeRegistration) {
                $timeRegistrations['start_time'][] = $timeRegistration->actual_start_time ? date('H:i', strtotime($timeRegistration->actual_start_time)) : '';
                $timeRegistrations['start_dimona_status'][] = $messages[$planning->id % 4];
                $timeRegistrations['end_time'][] = $timeRegistration->actual_end_time ? date('H:i', strtotime($timeRegistration->actual_end_time)) : '';
                $timeRegistrations['end_dimona_status'][] = $timeRegistration->actual_end_time ? $messages[$planning->id % 4] : null;
                $count += 1;
            }
            $response['planning_data'][$planning->workStation->id]['plannings'][] = [
                'employee_id'          => $planning->employeeProfile->id,
                'employee_name'        => $employeeName,
                'function_name'        => $planning->functionTitle->name,
                'start_time'           => date('H:i', strtotime($planning->start_date_time)),
                'actual_start_timings' => $timeRegistrations['start_time'],
                'start_dimona_status'  => $timeRegistrations['start_dimona_status'],
                'end_time'             => date('H:i', strtotime($planning->end_date_time)),
                'actual_end_timings'   => $timeRegistrations['end_time'],
                'end_dimona_status'    => $timeRegistrations['end_dimona_status'],
                'break_timings'        => [],
                'cost'                 => 10,
                'count'                => $count
            ];
            $response['planning_data'][$planning->workStation->id]['count'] += $count;
        }
        $response['planning_data'] = array_values($response['planning_data']);
        return $response;
    }
}
