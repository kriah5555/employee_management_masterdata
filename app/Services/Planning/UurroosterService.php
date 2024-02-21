<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Company\CompanyService;
use App\Services\Company\DashboardAccessService;
use App\Models\Company\Location;
use App\Services\Company\Absence\AbsenceService;
use App\Services\Company\LocationService;
use App\Models\Planning\Vacancy;

class UurroosterService
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
        $location_id = $values['location_id'] ?? null;
        if ($values['access_type'] == 'company') {
            $locations = app(LocationService::class)->getActiveLocations();
            $locations = $locations->map(function ($item) {
                return [
                    'value' => $item->id,
                    'label' => $item->location_name,
                ];
            })->toArray();
            if (!$location_id && count($locations)) {
                $location_id = end($locations)['value'];
            }
        } else {
            $locations = app(LocationService::class)->getLocationById($location_id);
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
        if ($location_id) {
            $qr_token = [
                'company_id'  => getCurrentCompanyId(),
                'location_id' => $location_id,
            ];
            $qr_token = encodeData($qr_token);
            //Week dates.
            $location = Location::find($location_id);
            $workstations = $location ? $location->workstations : null;
            $response['qr_token'] = $qr_token;
            $response['location_name'] = $location->location_name;
            $response['planning_data'] = [];
            foreach ($workstations as $value) {
                $response['planning_data'][$value->id]['workstation_id'] = $value->id;
                $response['planning_data'][$value->id]['workstation_name'] = $value->workstation_name;
                $response['planning_data'][$value->id]['count'] = 0;
                $response['planning_data'][$value->id]['plannings'] = [];
            }
            $data = $this->planningRepository->getPlansBetweenDates($location_id, [], [], $date, $date, '', ['workStation', 'employeeProfile.user', 'employeeType.employeeTypeConfig', 'functionTitle', 'timeRegistrations']);
            return $this->formatUurroosterData($data->merge($this->getVacancies($location_id), '', $date), $response);
        } else {
            $response['qr_token'] = null;
            $response['location_name'] = 'No locations';
            $response['planning_data'] = [];
            return $response;
        }
    }

    public function getVacancies($location_id, $workstation_id = '', $date = '')
    {
        return Vacancy::when(!empty($location_id), fn ($query) => $query->where('location_id', $location_id))
                        ->when(!empty($workstation_id), fn ($query) => $query->where('location_id', $workstation_id))
                        ->when(!empty($date), fn ($query) => $query->where('start_date', formatDate($date, 'Y-m-d')))
                        ->get();

    }

    public function formatUurroosterData($plannings, $response)
    {
        $absenceService = app(AbsenceService::class);
        foreach ($plannings as $planning) {
            $class = class_basename($planning);

            $timeRegistrations = [
                'start_time'          => [],
                'start_dimona_status' => [],
                'end_time'            => [],
                'end_dimona_status'   => [],
            ];
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

            if ($class == 'PlanningBase') {
                $employeeName   = $planning->employeeProfile->full_name;
                $workstation_id = $planning->workstation_id;
                $absence        = $absenceService->getAbsenceForDate($planning->plan_date, '', $planning->employee_profile_id);
                $absence_status = $absence->isNotEmpty();
                $absence_codes  = $absence->isNotEmpty() ? $absence->pluck('absenceHours')->flatten()->pluck('holidayCode.holiday_code_name')->filter()->implode(', ') : null;
                $start_time     = $planning->start_time;
                $end_time       = $planning->end_time;
                $employee_type  = $planning->employeeType->name;
                $employee_type_color = $planning->employeeType->employeeTypeConfig->icon_color;
                $function_name = $planning->functionTitle->name;
                if ($planning->timeRegistrations->count()) {
                    $count = 0;
                } else {
                    $count = 1;
                }
                foreach ($planning->timeRegistrations as $timeRegistration) {
                    $timeRegistrations['start_time'][] = $timeRegistration->actual_start_time ? date('H:i', strtotime($timeRegistration->actual_start_time)) : '';
                    $timeRegistrations['start_dimona_status'][] = $messages[$planning->id % 4];
                    $timeRegistrations['end_time'][] = $timeRegistration->actual_end_time ? date('H:i', strtotime($timeRegistration->actual_end_time)) : '';
                    if ($timeRegistration->actual_end_time) {
                        $timeRegistrations['end_dimona_status'][] = $messages[$planning->id % 4];
                    }
                    $count += 1;
                }
            } else {
                $employeeName        = $planning->name;
                $workstation_id      = $planning->workstation_id;
                $absence_status      = false;
                $absence_codes       = null;
                $start_time          = formatDate($planning->start_time, 'H:i');
                $end_time            = formatDate($planning->end_time, 'H:i');
                $employee_type       = 'Open Shifts';
                $employee_type_color = null;
                $function_name       = $planning->functions->name;
                $count               = 1;
            }

            $response['planning_data'][$workstation_id]['plannings'][] = [
                'employee_id'           => $planning->employee_profile_id,
                'employee_type'         => $employee_type,
                'employee_type_color'   => $employee_type_color,
                'employee_name'         => $employeeName,
                'function_name'         => $function_name,
                'start_time'            => $start_time,
                'actual_start_timings'  => $timeRegistrations['start_time'],
                'start_dimona_status'   => $timeRegistrations['start_dimona_status'],
                'end_time'              => $end_time,
                'actual_end_timings'    => $timeRegistrations['end_time'],
                'end_dimona_status'     => $timeRegistrations['end_dimona_status'],
                'break_timings'         => [],
                'cost'                  => '',
                'absence_status'        => $absence_status,
                'absence_holiday_codes' => $absence_codes,
                'count'                 => $count
            ];
            $response['planning_data'][$workstation_id]['count'] += $count;
        }
        $response['planning_data'] = array_values($response['planning_data']);
        return $response;
    }
}
