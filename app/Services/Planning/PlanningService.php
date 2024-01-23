<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Repositories\Planning\PlanningRepository;
use App\Models\Company\Location;
use App\Models\Company\Company;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Services\Employee\EmployeeService;
use App\Services\Planning\PlanningContractService;
use App\Services\Planning\PlanningShiftsService;
use App\Services\Company\Absence\AbsenceService;


class PlanningService implements PlanningInterface
{

    public function __construct(
        protected PlanningBase $planningBase,
        protected Location $location,
        protected Company $company,
        protected EmployeeType $employeeType,
        protected FunctionTitle $functionTitle,
        protected EmployeeService $employeeService,
        protected PlanningRepository $planningRepository,
        protected PlanningContractService $planningContractService,
        protected PlanningShiftsService $planningShiftsService,
    ) {
    }

    /**
     * Get employee type of the company
     *
     * @param  [type] $companyId
     * @return array
     */
    public function getEmployeeTypes($companyId)
    {
        $response = [];
        $employeeTypes = [];
        $data = $this->company->employeeTypes($companyId)->get()->toArray();

        if (count($data) > 0) {
            $data = reset($data);
            foreach ($data['sectors'] as $values) {
                $response += $values['employee_types_value'];
            }
        }
        return $response;
    }

    /**
     * Formating the options
     *
     * @param  [type] $data
     * @return array
     */
    public function optionsFormat($data)
    {
        $response = [];
        foreach ($data as $value) {
            $response[$value['value']] = $value['label'];
        }
        return $response;
    }

    /**
     * Workstations data format.
     *
     * @param  [type] $data
     * @return array
     */
    public function getWorkstations()
    {
        $data = $response = [];
        $data = $this->location->with(['workstationsValues'])->get()->toArray();

        foreach ($data as $value) {
            //$response[$value['id']]['id'] = $value['id'];
            //$response[$value['id']]['name'] = $value['location_name'];
            $response[$value['id']] = $value['workstations_values'];
        }
        return $response;
    }

    /**
     * Function indexing by id
     *
     * @param  [type] $data
     * @return array
     */
    public function functionFormat($data)
    {
        $response = [];
        foreach ($data as $value) {
            $response[$value['id']] = $value;
        }
        return $response;
    }

    public function getPlanningOverviewFilterService($companyId)
    {
        $output['locations'] = $this->location->all(['id as value', 'location_name as label'])->toArray();

        //$response['locations'] = $this->optionsFormat($output['locations']);
        $response['locations'] = $output['locations'];
        $response['workstations'] = $this->getWorkstations();
        $response['employee_types'] = $this->getEmployeeTypes($companyId);

        return $response;
    }

    public function getMonthlyPlanningService($year, $month, $location, $workstations, $employee_types)
    {
        $response = [];
        $data = $this->getMonthlyPlanningDayCount($location, $workstations, $employee_types, $month, $year);
        // $data = $this->planningBase->monthPlanning($year, $month, $location, $workstations, $employee_types);
        foreach ($data as $value) {

            $response[date('d-m-Y', strtotime($value['date']))] = $value['count'];
        }
        return $response;
    }

    public function employeeTypeFormat(array $employeeTypeDetails)
    {
        $formattedEmployeeDetails = [];
        foreach ($employeeTypeDetails as $value) {
            $formattedEmployeeDetails[$value['id']]['id'] = $value['id'];
            $formattedEmployeeDetails[$value['id']]['name'] = $value['name'];
            $formattedEmployeeDetails[$value['id']]['category'] = $value['employee_type_category_id'];
            $formattedEmployeeDetails[$value['id']]['color'] = $value['employee_type_config']['icon_color'];
        }
        return $formattedEmployeeDetails;
    }

    public function employeeProfilesFormat(array $employee)
    {
        $employeeFormat = [];
        foreach ($employee as $value) {
            $user = $value['user'];
            $name = $user['user_basic_details']['first_name'] . ' ' . $user['user_basic_details']['last_name'];
            $employeeFormat[$value['id']]['employee_id'] = $value['id'];
            // $employeeFormat[$value['id']]['user_id'] = $value['user_id'];
            // $employeeFormat[$value['id']]['social_security_number'] = $user['social_security_number'];
            $employeeFormat[$value['id']]['employee_name'] = $name;
            // $employeeFormat[$value['id']]['gender'] = $user['user_basic_details']['gender_id'];
        }
        return $employeeFormat;
    }

    public function formatWeeklyData($plannings, &$response)
    {
        foreach ($plannings as $plan) {
            $workstationId = $plan->workstation_id;
            $contractHours = $plan->contract_hours;
            $planDate = date('d-m-Y', strtotime($plan->start_date_time));
            //Initializing.
            $profile = $plan->employee_profile_id;

            //Employee details.
            if (!isset($response['workstation_data'][$workstationId]['employee'][$profile])) {
                $response['workstation_data'][$workstationId]['employee'][$profile] = [
                    'employee_id'   => $plan->employeeProfile->id,
                    'employee_name' => $plan->employeeProfile->user->userBasicDetails->first_name . ' ' . $plan->employeeProfile->user->userBasicDetails->last_name
                ];
                $response['workstation_data'][$workstationId]['employee'][$profile]['employee_types'][$plan->employeeType->name] = $plan->employeeType->employeeTypeConfig->icon_color;
                $response['workstation_data'][$workstationId]['employee'][$profile]['total'] = [
                    'cost'           => 0,
                    'contract_hours' => 0
                ];
            }
            $planDetails = [
                "plan_id"        => $plan->id,
                "timings"        => date('H:i', strtotime($plan->start_date_time)) . ' ' . date('H:i', strtotime($plan->end_date_time)),
                "contract_hours" => number_format($contractHours, 2, ',', '.'),
            ];

            if (!isset($response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate])) {
                $response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['planning'] = [];
                $response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['contract_hours'] = 0;
                $response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['cost'] = 0;
                $response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['employee_type'] = [
                    'value' => $plan->employeeType->id,
                    'label' => $plan->employeeType->name,
                ];
                $response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['function'] = [
                    'value' => $plan->functionTitle->id,
                    'label' => $plan->functionTitle->name,
                ];
            }
            $response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['planning'][] = $planDetails;
            $response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['workstation_data'][$workstationId]['employee'][$profile]['plans'][$planDate]['contract_hours']) + $contractHours
            );
            $response['workstation_data'][$workstationId]['employee'][$profile]['total']['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['workstation_data'][$workstationId]['employee'][$profile]['total']['contract_hours']) + $contractHours
            );
            if (!isset($response['total'][$planDate])) {
                $response['total'][$planDate] = [
                    'cost'           => 0,
                    'contract_hours' => 0
                ];
            }
            $response['total'][$planDate]['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['total'][$planDate]['contract_hours']) + $contractHours
            );
            $response['total']['week_total']['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['total']['week_total']['contract_hours']) + $contractHours
            );
        }

        $response['workstation_data'] = array_values($response['workstation_data']);
        foreach ($response['workstation_data'] as $id => $value) {
            $response['workstation_data'][$id]['employee'] = array_values($value['employee']);
        }
        return $response;
    }

    public function getWeeklyPlanningService($location, $workstations, $employee_types, $weekNo, $year)
    {
        $response = [
            'workstation_data' => [],
            'total'            => [],
        ];
        //Week dates.
        $workstationsRaw = $this->location->with('workstationsValues')->get()->toArray();
        $workstationsRaw = $this->getWorkstations($workstationsRaw);
        foreach ($workstationsRaw[$location] as $value) {
            $response['workstation_data'][$value['value']]['workstation_id'] = $value['value'];
            $response['workstation_data'][$value['value']]['workstation_name'] = $value['label'];
            $response['workstation_data'][$value['value']]['employee'] = [];
            $shifts = $this->planningShiftsService->getPlanningShifts($location, $value['value']);
            $shiftsFormatted = [];
            foreach ($shifts as $shift) {
                $shiftsFormatted[] = [
                    'id'             => $shift->id,
                    'start_time'     => date('H:i', strtotime($shift->start_time)),
                    'end_time'       => date('H:i', strtotime($shift->end_time)),
                    'contract_hours' => numericToEuropean($shift->contract_hours),
                    'time'           => date('H:i', strtotime($shift->start_time)) . '-' . date('H:i', strtotime($shift->end_time))
                ];
            }
            $response['workstation_data'][$value['value']]['shifts'] = $shiftsFormatted;
        }
        $weekDates = getWeekDates($weekNo, $year);
        foreach ($weekDates as $date) {
            $date = date('d-m-Y', strtotime($date));
            $response['total'][$date] = [
                'cost'           => 0,
                'contract_hours' => 0
            ];
        }
        $response['total']['week_total'] = [
            'cost'           => 0,
            'contract_hours' => 0
        ];

        //Getting the data from the query.
        $plannings = $this->getWeeklyPlannings($location, $workstations, $employee_types, $weekNo, $year);
        $response = $this->formatWeeklyData($plannings, $response);
        return $response;
    }

    public function getDayPlanningService($location, $workstations, $employee_types, $date)
    {
        $plannings = $this->getDayPlannings($location, $workstations, $employee_types, $date);
        return $this->formatDayPlanning($plannings);
    }

    public function getDayPlanningMobileService($location, $workstations, $employee_types, $date)
    {
        $plannings = $this->getDayPlannings($location, $workstations, $employee_types, $date);
        $absenceService = app(AbsenceService::class);
        return $plannings->map(function ($plan) use ($absenceService) {
            $leave_status = $absenceService->getAbsenceForDate($plan->plan_date)->isNotEmpty();
            return [
                'plan_id'                  => $plan->id,
                'plan_date'                => $plan->plan_date,
                'start_time'               => $plan->start_time,
                'end_time'                 => $plan->end_time,
                'contract_hours'           => $plan->contract_hours,
                'contract_hours_formatted' => $plan->contract_hours_formatted,
                'location_id'              => $plan->location_id,
                'location_name'            => $plan->location->location_name,
                'workstation_id'           => $plan->workstation_id,
                'workstation_name'         => $plan->workstation->workstation_name,
                'function_id'              => $plan->function_id,
                'function_name'            => $plan->functionTitle->name,
                'employee_profile_id'      => $plan->employee_profile_id,
                'employee_name'            => $plan->employeeProfile->full_name,
                'employee_type_id'         => $plan->employee_type_id,
                'employee_type'            => $plan->employeeType->name,
                'leave_status'             => $leave_status,
                'leave_reason'             => $leave_status ? "Something" : null,
            ];
        });
    }

    public function getEmployeeDayPlanningService($employee_profile_id, $date = '')
    {
        $date = $date == '' ? date('d-m-Y') : $date;
        $plannings = $this->getDayPlannings($location, $workstations, $employee_types, $date);
        return $this->formatDayPlanning($plannings);
    }

    public function formatDayPlanning($plannings)
    {
        $response = [];
        foreach ($plannings as $plan) {
            if (!isset($response[$plan->employee_profile_id])) {
                $response[$plan->employee_profile_id] = [
                    'employee_id'   => $plan->employeeProfile->id,
                    'employee_name' => $plan->employeeProfile->user->userBasicDetails->first_name . ' ' . $plan->employeeProfile->user->userBasicDetails->last_name,
                    'plans'         => []
                ];
            }
            $response[$plan->employee_profile_id]['plans'][] = [
                'plan_id'          => $plan->id,
                'start_time'       => date('H:i', strtotime($plan->start_date_time)),
                'end_time'         => date('H:i', strtotime($plan->end_date_time)),
                'contract_hours'   => $plan->contract_hours,
                'workstation_name' => $plan->workStation->workstation_name,
                'function_name'    => $plan->functionTitle->name,
            ];
        }
        return array_values($response);
    }

    public function planningCreateOptionsService($workstation, $employeeId)
    {

    }

    public function getPlanningDetailsById($planId)
    {
        return $this->formatPlanDetails(
            $this->planningRepository->getPlanningById($planId, [
                'employeeType',
                'workstation',
                'functionTitle',
                'employeeProfile',
                'employeeProfile.user.userBasicDetails',
                'timeRegistrations',
                'timeRegistrations.startedBy',
                'timeRegistrations.endedBy',
                'contracts',
                'breaks'
            ])
        );
    }

    public function getPlanningDetailsForDayOverview($planId)
    {
        return $this->formatPlanDetailsForDayOverview(
            $this->planningRepository->getPlanningById($planId, [
                'employeeType',
                'workstation',
                'functionTitle',
                'employeeProfile',
                'employeeProfile.user.userBasicDetails',
                'timeRegistrations',
                'timeRegistrations.startedBy',
                'timeRegistrations.endedBy',
                'contracts',
                'breaks'
            ])
        );
    }
    public function getPlanningById($planId)
    {
        return $this->planningRepository->getPlanningById($planId, [
            'employeeType',
            'workstation',
            'functionTitle',
            'employeeProfile',
            'employeeProfile.user.userBasicDetails',
            'timeRegistrations',
            'timeRegistrations.startedBy',
            'timeRegistrations.endedBy',
            'contracts',
            'breaks'
        ]);
    }

    public function formatPlanDetails($details)
    {
        $startPlan = $stopPlan = false;
        $sendPlanDimona = $details->dimona_status ? false : true;
        if (strtotime($details->start_date_time) <= strtotime(date('Y-m-d H:i')) && strtotime($details->end_date_time) >= strtotime(date('Y-m-d H:i'))) {
            if ($details->plan_started) {
                $startPlan = false;
                $stopPlan = true;
            } else {
                $startPlan = true;
                $stopPlan = false;
            }
        }
        $response = [
            'start_time'       => date('H:i', strtotime($details->start_date_time)),
            'end_time'         => date('H:i', strtotime($details->end_date_time)),
            'employee_type'    => $details->employeeType->name,
            'function'         => $details->functionTitle->name,
            'workstation'      => $details->workstation->workstation_name,
            'start_plan'       => $startPlan,
            'stop_plan'        => $stopPlan,
            'send_plan_dimona' => $sendPlanDimona,
            'contract'         => $this->planningContractService->getPlanningContractContract($details),
        ];
        $response['activity'] = [];
        foreach ($details->timeRegistrations as $timeRegistrations) {
            $startedByFullName = $timeRegistrations->startedBy->userBasicDetails->first_name . ' ' . $timeRegistrations->startedBy->userBasicDetails->last_name;
            $response['activity'][] = "Plan started by " . $startedByFullName . " at " . date('H:i', strtotime($timeRegistrations->actual_start_time));
            if ($timeRegistrations->actual_end_time) {
                $endedByFullName = $timeRegistrations->endedBy->userBasicDetails->first_name . ' ' . $timeRegistrations->endedBy->userBasicDetails->last_name;
                $response['activity'][] = "Plan stopped by " . $endedByFullName . " at " . date('H:i', strtotime($timeRegistrations->actual_end_time));
            }
        }
        return $response;
    }

    public function formatPlanDetailsForDayOverview($details)
    {
        $startPlan = $stopPlan = false;
        $sendPlanDimona = $details->dimona_status ? false : true;
        // if (strtotime($details->start_date_time) <= strtotime(date('Y-m-d H:i')) && strtotime($details->end_date_time) >= strtotime(date('Y-m-d H:i'))) {
            // if ($details->plan_started) {
            //     $startPlan = false;
            //     $stopPlan = true;
            // } else {
            //     $startPlan = true;
            //     $stopPlan = false;
            // }

            // if (strtotime($details->end_date_time) >= strtotime(date('Y-m-d H:i'))) {
            //     $startPlan = false;
            // }
            
        // }

        // Get the current date and time
        $currentDateTime = strtotime(date('Y-m-d H:i'));

        // Initialize variables for start and stop plans
        $startPlan = $stopPlan = false;

        // Check if the start time is less than or equal to the current time
        // if (strtotime($details->start_date_time) <= $currentDateTime) {
        //     $startPlan = $stopPlan = false; // Don't start or stop the plan
        // } 
        // if (strtotime($details->end_date_time) > $currentDateTime && strtotime($details->start_date_time) > $currentDateTime) {
        //     $startPlan = $stopPlan = false; // Don't start or stop the plan
        // }
        
        // if current time is in between start and end time
        if ($currentDateTime >= strtotime($details->start_date_time) && $currentDateTime <= strtotime($details->end_date_time)) {
            $startPlan = true; 
            $stopPlan  = false; 
        } 

        // Check if the plan has already been started
        if ($details->plan_started) {
            $startPlan = false; // Don't start the plan
            $stopPlan  = true;  // Stop the plan
        }

        $response = [
            'start_time'       => date('H:i', strtotime($details->start_date_time)),
            'end_time'         => date('H:i', strtotime($details->end_date_time)),
            'employee_type'    => $details->employeeType->name,
            'function'         => $details->functionTitle->name,
            'workstation'      => $details->workstation->workstation_name,
            'start_plan'       => $startPlan,
            'stop_plan'        => $stopPlan,
            'send_plan_dimona' => $sendPlanDimona,
            'contract'         => $this->planningContractService->getPlanningContractContract($details),
        ];
        $response['activity'] = [];
        foreach ($details->timeRegistrations as $timeRegistrations) {
            $startedByFullName = $timeRegistrations->startedBy->userBasicDetails->first_name . ' ' . $timeRegistrations->startedBy->userBasicDetails->last_name;
            $response['activity'][] = "Plan started by " . $startedByFullName . " at " . date('H:i', strtotime($timeRegistrations->actual_start_time));
            if ($timeRegistrations->actual_end_time) {
                $endedByFullName = $timeRegistrations->endedBy->userBasicDetails->first_name . ' ' . $timeRegistrations->endedBy->userBasicDetails->last_name;
                $response['activity'][] = "Plan stopped by " . $endedByFullName . " at " . date('H:i', strtotime($timeRegistrations->actual_end_time));
            }
        }
        return $response;
    }

    public function getWeeklyPlannings($location, $workstations, $employee_types, $weekNumber, $year, $employee_profile_id = '')
    {
        $weekDates = getWeekDates($weekNumber, $year);
        $startDateOfWeek = reset($weekDates);
        $endDateOfWeek = end($weekDates);
        return $this->planningRepository->getPlansBetweenDates($location, $workstations, $employee_types, $startDateOfWeek, $endDateOfWeek, $employee_profile_id, ['workStation', 'employeeProfile.user', 'employeeType', 'functionTitle']);
    }

    public function getDayPlannings($location, $workstations, $employee_types, $date, $employee_profile_id = '')
    {
        return $this->planningRepository->getPlansBetweenDates($location, $workstations, $employee_types, $date, $date, '', ['workStation', 'employeeProfile.user', 'employeeType', 'functionTitle']);
    }

    public function getPlans($from_date = '', $to_date = '', $location = '', $workstations = '', $employee_types = '', $employee_id = '', $relations = [])
    {
        return $this->planningRepository->getPlans($from_date, $to_date, $location, $workstations, $employee_types, $employee_id, $relations);
    }

    public function getPlansForAbsence($dates_array, $employee_profile_id)
    {
        $return_data = [];
        $plans = $this->planningRepository->getPlansByDatesArray($dates_array, $employee_profile_id);

        foreach ($plans as $plan) {
            // $return_data[$plan->start_time . '-' . $plan->end_time . '-' . $plan->contract_hours_formatted] = $plan->start_time . '-' . $plan->end_time . ' ' . $plan->contract_hours_formatted;
            // $return_data[$plan->start_time . '-' . $plan->end_time . '-' . $plan->contract_hours_formatted] = $plan->start_time . '-' . $plan->end_time . ' ' . $plan->contract_hours_formatted;
            $return_data[$plan->start_time . '-' . $plan->end_time . '-' . $plan->contract_hours_formatted] = [
                'plan_id'     => $plan->start_time . '-' . $plan->end_time . '-' . $plan->contract_hours_formatted,
                'Plan_time'   => $plan->start_time . '-' . $plan->end_time . ' ' . $plan->contract_hours_formatted,
                'shift_leave' => false,
            ];
        }
        return array_values($return_data);
    }

    public function getMonthlyPlanningDayCount($location, $workstations, $employee_types, $month, $year)
    {
        $monthDates = getStartAndEndDateOfMonth($month, $year);
        return $this->planningRepository->getMonthlyPlanningDayCount($location, $workstations, $employee_types, $monthDates['start_date'], $monthDates['end_date']);
    }
    public function getWeeklyPlanningForEmployee($employeId, $location, $workstations, $employee_types, $weekNo, $year)
    {
        //Getting the data from the query.
        $plannings = $this->getWeeklyPlannings($location, $workstations, $employee_types, $weekNo, $year, $employeId);
        $employeeDetails = $this->employeeService->getEmployeeDetails($employeId);
        $response = [
            'employee_id'   => $employeId,
            'employee_name' => $employeeDetails['first_name'] . ' ' . $employeeDetails['last_name'],
            'total'         => [
                'cost'           => 0,
                'contract_hours' => 0
            ],
            'plans'         => []
        ];
        $this->getTotalsForWeeklyPlanning($location, $workstations, $employee_types, $weekNo, $year, $response);
        return $this->formatWeeklyDataEmployee($plannings, $response);
    }

    public function getTotalsForWeeklyPlanning($location, $workstations, $employee_types, $weekNo, $year, &$response)
    {
        $response['day_total'] = [];
        $weekDates = getWeekDates($weekNo, $year);
        foreach ($weekDates as $date) {
            $date = date('d-m-Y', strtotime($date));
            $response['day_total'][$date] = [
                'cost'           => 0,
                'contract_hours' => 0
            ];
        }
        $response['day_total']['week_total'] = [
            'cost'           => 0,
            'contract_hours' => 0
        ];

        //Getting the data from the query.
        $plannings = $this->getWeeklyPlannings($location, $workstations, $employee_types, $weekNo, $year);
        foreach ($plannings as $plan) {
            $planDate = date('d-m-Y', strtotime($plan->start_date_time));
            $response['day_total'][$planDate]['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['day_total'][$planDate]['contract_hours']) + $plan->contract_hours
            );
            $response['day_total']['week_total']['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['day_total']['week_total']['contract_hours']) + $plan->contract_hours
            );
        }
        return $response;
    }
    public function formatWeeklyDataEmployee($plannings, &$response)
    {
        foreach ($plannings as $plan) {
            $contractHours = $plan->contract_hours;
            $planDate = date('d-m-Y', strtotime($plan->start_date_time));
            $planDetails = [
                "plan_id"        => $plan->id,
                "timings"        => date('H:i', strtotime($plan->start_date_time)) . ' ' . date('H:i', strtotime($plan->end_date_time)),
                "contract_hours" => number_format($contractHours, 2, ',', '.'),
            ];

            if (!isset($response['plans'][$planDate])) {
                $response['plans'][$planDate]['planning'] = [];
                $response['plans'][$planDate]['contract_hours'] = 0;
                $response['plans'][$planDate]['cost'] = 0;
                $response['plans'][$planDate]['employee_type'] = [
                    'value' => $plan->employeeType->id,
                    'label' => $plan->employeeType->name,
                ];
                $response['plans'][$planDate]['function'] = [
                    'value' => $plan->functionTitle->id,
                    'label' => $plan->functionTitle->name,
                ];
            }
            $response['plans'][$planDate]['planning'][] = $planDetails;
            $response['plans'][$planDate]['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['plans'][$planDate]['contract_hours']) + $contractHours
            );
            $response['total']['contract_hours'] = numericToEuropean(
                europeanToNumeric($response['total']['contract_hours']) + $contractHours
            );
        }
        return $response;
    }

    public function getPlansToSendDimona($values)
    {
        $response = [];
        $companyId = getCompanyId();
        $company = Company::findOrFail($companyId);
        $activeDimonaEmployeeTypes = $company->dimoanEmployeeTypes->pluck('id')->toArray();
        $plans = $this->planningRepository->getPlansBetweenDates($values['location_id'], [], '', $values['date'], $values['date'], '', ['employeeProfile.user.userBasicDetails']);
        foreach ($plans as $plan) {
            if (!$plan->dimona_status && in_array($plan->employee_type_id, $activeDimonaEmployeeTypes)) {
                $response[] = [
                    'id' => $plan->id,
                    'name'    => $plan->employeeProfile->user->userBasicDetails->first_name . ' ' . $plan->employeeProfile->user->userBasicDetails->last_name,
                    'timings' => date('H:i', strtotime($plan->start_date_time)) . '-' . date('H:i', strtotime($plan->end_date_time)) . ' ' . numericToEuropean($plan->contract_hours),
                ];
            }
        }
        return $response;
    }
}
