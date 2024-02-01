<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Company\CompanyService;
use App\Services\Company\DashboardAccessService;
use App\Models\Company\Location;
use App\Services\Company\Absence\AbsenceService;
use App\Services\Company\LocationService;

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
        $response = [
            'location_selection' => $values['location_selection']
        ];
        if ($values['access_type'] == 'company') {
            $locations = app(LocationService::class)->getActiveLocations();
            $locations = $locations->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->location_name,
                ];
            })->toArray();
            if (!$values['location_id'] && count($locations)) {
                $values['location_id'] = end($locations)->id;
            }
        } else {
            $locations = app(LocationService::class)->getLocationById($values['location_id']);
            $locations = [
                [
                    'value' => $locations->id,
                    'label' => $locations->location_name,
                ]
            ];
        }
        if ($values['location_selection']) {
            $response['locations'] = $locations;
        }
        $date = date('Y-m-d', strtotime($values['date']));
        if ($values['location_id']) {
            $qr_token = [
                'company_id'  => getCurrentCompanyId(),
                'location_id' => $values['location_id'],
            ];
            $qr_token = encodeData($qr_token);
            //Week dates.
            $workstationsRaw = Location::find($values['location_id'])->with('workstations')->first();
            $response['qr_token'] = $qr_token;
            $response['location_name'] = $workstationsRaw->location_name;
            $response['planning_data'] = [];
            foreach ($workstationsRaw['workstations'] as $value) {
                $response['planning_data'][$value->id]['workstation_id'] = $value->workstation_name;
                $response['planning_data'][$value->id]['workstation_name'] = $value->workstation_name;
                $response['planning_data'][$value->id]['count'] = 0;
                $response['planning_data'][$value->id]['plannings'] = [];
            }
            $data = $this->planningRepository->getPlansBetweenDates($values['location_id'], [], [], $date, $date, '', ['workStation', 'employeeProfile.user', 'employeeType.employeeTypeConfig', 'functionTitle', 'timeRegistrations']);
            return $this->formatUurroosterData($data, $response);
        } else {
            $response['qr_token'] = null;
            $response['location_name'] = 'No locations';
            $response['planning_data'] = [];
            return $response;
        }
    }
    public function formatUurroosterData($plannings, $response)
    {
        $absenceService = app(AbsenceService::class);
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
                if ($timeRegistration->actual_end_time) {
                    $timeRegistrations['end_dimona_status'][] = $messages[$planning->id % 4];
                }
                $count += 1;
            }
            $absence = $absenceService->getAbsenceForDate($planning->plan_date, '', $planning->employee_profile_id);
            $response['planning_data'][$planning->workStation->id]['plannings'][] = [
                'employee_id'           => $planning->employee_profile_id,
                'employee_type'         => $planning->employeeType->name,
                'employee_type_color'   => $planning->employeeType->employeeTypeConfig->icon_color,
                'employee_name'         => $employeeName,
                'function_name'         => $planning->functionTitle->name,
                'start_time'            => date('H:i', strtotime($planning->start_date_time)),
                'actual_start_timings'  => $timeRegistrations['start_time'],
                'start_dimona_status'   => $timeRegistrations['start_dimona_status'],
                'end_time'              => date('H:i', strtotime($planning->end_date_time)),
                'actual_end_timings'    => $timeRegistrations['end_time'],
                'end_dimona_status'     => $timeRegistrations['end_dimona_status'],
                'break_timings'         => [],
                'cost'                  => '',
                'absence_status'        => $absence->isNotEmpty(),
                'absence_holiday_codes' => $absence->isNotEmpty() ? $absence->pluck('absenceHours')->flatten()->pluck('holidayCode.holiday_code_name')->filter()->implode(', ') : null,
                'count'                 => $count
            ];
            $response['planning_data'][$planning->workStation->id]['count'] += $count;
        }
        $response['planning_data'] = array_values($response['planning_data']);
        return $response;
    }
}
