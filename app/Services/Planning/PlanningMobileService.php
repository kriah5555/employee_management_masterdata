<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Employee\EmployeeContractService;
use App\Services\Planning\PlanningService;
use App\Repositories\Company\CompanyRepository;
use App\Models\Company\Employee\EmployeeProfile;

class PlanningMobileService
{

    public function __construct(
        protected PlanningService $planningService,
        protected CompanyRepository $companyRepository,
        protected EmployeeContractService $employeeContractService,
        protected PlanningRepository $planningRepository,
    ) {
    }

    public function getWeeklyPlanningService($location = '', $workstations = [], $employee_types = '', $weekNumber, $year, $company_ids, $user_id)
    {
        $response = [];

        $weekDates = getWeekDates($weekNumber, $year);

        $dates = getDatesArray(formatDate(reset($weekDates)), formatDate(end($weekDates)));

        $format = array_map(
            function ($date) {
                return [
                    'plan_date'   => $date,
                    'plan_shifts' => [],
                ];
            },
            $dates
        );

        $response = array_combine(array_column($format, 'plan_date'), $format); # will add keys as dates

        foreach ($company_ids as $company_id) {
            setTenantDBByCompanyId($company_id);

            $employee_profiles = EmployeeProfile::where('user_id', $user_id)->get()->first();

            if (!empty($employee_profiles)) {

                $company = $this->companyRepository->getCompanyById($company_id);
                $plans = $this->planningService->getWeeklyPlannings('', '', '', $weekNumber, $year, $employee_profiles->id);
                $response = $this->formatPlans($plans, $company, $response);
            }
        }

        return array_values($response);
    }

    public function formatPlans($plans, $company, $format = [])
    {
        $return = !empty($format) ? $format : [];
        $company_id = $company->id;
        $company_name = $company->company_name;

        foreach ($plans as $plan) {
            $return[$plan->plan_date]['plan_shifts'][] = [
                'plan_id'                  => $plan->id,
                'plan_date'                => $plan->plan_date,
                'employee_profile_id'      => $plan->employee_profile_id,
                'company_id'               => $company_id,
                'company_name'             => $company_name,
                'location_id'              => $plan->location_id,
                'location_name'            => $plan->location->location_name,
                'workstation_id'           => $plan->location_id,
                'workstation_name'         => $plan->workStation->workstation_name,
                'function_id'              => $plan->function_id,
                'function_name'            => $plan->functionTitle->name,
                'start_time'               => $plan->start_time,
                'end_time'                 => $plan->end_time,
                'contract_hours'           => $plan->contract_hours,
                'contract_hours_formatted' => $plan->contract_hours_formatted,
            ];
        }

        return $return;
    }

    public function getDatesPlanningService($company_ids, $user_id, $dates)
    {
        $response = [];

        $format = array_map(
            function ($date) {
                return [
                    'plan_date'   => $date,
                    'plan_shifts' => [],
                ];
            },
            $dates
        );

        $response = array_combine(array_column($format, 'plan_date'), $format); # will add keys as dates

        foreach ($company_ids as $company_id) {
            setTenantDBByCompanyId($company_id);

            $employee_profiles = EmployeeProfile::where('user_id', $user_id)->get()->first();

            if (!empty($employee_profiles)) {

                $company = $this->companyRepository->getCompanyById($company_id);
                foreach ($dates as $date) {
                    $plans = $this->planningService->getPlans($date, $date, '', '', '', $employee_profiles->id);
                    $response = $this->formatPlans($plans, $company, $response);
                }
            }
        }

        return array_values($response);
    }

    public function getUserPlanningStatus($userId)
    {
        $companyIds = getUserCompanies($userId);
        $status = [
            'start' => 0,
            'break' => 0,
        ];
        foreach ($companyIds as $companyId) {
            connectCompanyDataBase($companyId);
            $employeeProfile = getEmployeeProfileByUserId($userId);
            if ($employeeProfile) {
                $newStatus = $this->getEmployeePlanningStatus($employeeProfile->id);
                $status['start'] = $newStatus['start'] ? 1 : $status['start'];
                $status['break'] = $newStatus['break'] ? 1 : $status['break'];
            }
        }
        return $status;
    }

    public function getEmployeePlanningStatus($employeeProfileId)
    {
        return $this->getEmployeeStartedPlanning($employeeProfileId);
    }

    public function getEmployeeStartedPlanning($employeeProfileId)
    {
        $response = [
            'start' => 0,
            'break' => 0,
        ];
        $plannings = PlanningBase::where('employee_profile_id', $employeeProfileId)
            ->where('plan_started', true)->first();
        if ($plannings) {
            $response['start'] = 1;
            if ($plannings->break_started) {
                $response['break'] = 1;
            }
        }
        return $response;
    }

    public function getEmployeeWorkedHours($company_ids, $user_id, $from_date, $to_date)
    {
        $response = [];
        foreach ($company_ids as $company_id) {
            setTenantDBByCompanyId($company_id);

            $employee_profiles = EmployeeProfile::where('user_id', $user_id)->get()->first();

            if (!empty($employee_profiles)) {
                $company = $this->companyRepository->getCompanyById($company_id);
                $plans = $this->planningService->getPlans($from_date, $to_date, '', '', '', $employee_profiles->id);
                if ($plans) {
                    $response = $this->formatWorkedHours($plans, $company, $response);
                }
            }
        }

        return $response;
    }

    public function formatWorkedHours($plans, $company)
    {
        $return = [];

        foreach ($plans as $plan) {
            $time_registrations = $plan->timeRegistrations;
            $breaks = $plan->breaks;

            $contract_hours = $plan->contract_hours;
            $planned_hours = $plan->planned_hours;
            $over_time = 0;
            $worked_hours = (($time_registrations) ? $time_registrations->flatten()->pluck('worked_hours')->sum() : 0);
            $break = ($breaks) ? $breaks->flatten()->pluck('break_hours')->sum() : 0;

            if (!isset($return[$plan->employeeType->name])) { # will add this data only once
                $return[$plan->employeeType->name] = [
                    'company_id'           => $company->id,
                    'company_name'         => $company->company_name,
                    'employee_type'        => $plan->employeeType->name,
                    'total_worked_hours'   => 0,
                    'total_planned_hours'  => 0,
                    'overtime'             => 0,
                    'total_contract_hours' => 0,
                    'break_hours'          => 0,
                ];
            }
            $return[$plan->employeeType->name]['total_worked_hours'] = ($return[$plan->employeeType->name]['total_worked_hours'] ?? 0) + $worked_hours;
            $return[$plan->employeeType->name]['total_contract_hours'] = ($return[$plan->employeeType->name]['total_contract_hours'] ?? 0) + $contract_hours;
            $return[$plan->employeeType->name]['total_planned_hours'] = ($return[$plan->employeeType->name]['total_planned_hours'] ?? 0) + $planned_hours;
            $return[$plan->employeeType->name]['overtime'] = ($return[$plan->employeeType->name]['overtime'] ?? 0) + $over_time;
            $return[$plan->employeeType->name]['break_hours'] = ($return[$plan->employeeType->name]['break_hours'] ?? 0) + $break;
            $return[$plan->employeeType->name]['plans'][] = [
                'plan_date'                => $plan->plan_date,
                'start_time'               => $plan->start_time,
                'end_time'                 => $plan->end_time,
                'contract_hours'           => $contract_hours,
                'contract_hours_formatted' => $plan->contract_hours_formatted,
                'worked_hours'             => $worked_hours,
                'worked_hours_formatted'   => formatToEuropeHours($worked_hours),
            ];
        }

        return array_values($return);
    }

    public function getEmployeesToSwitchPlan($values)
    {
        $planId = $values['plan_id'];
        $planDetails = $this->planningService->getPlanningById($planId);
        $employeeTypeId = $planDetails['employee_type_id'];
        $functionId = $planDetails['function_id'];
        $date = date('Y-m-d', strtotime($planDetails->start_date_time));
        $employeeContracts = $this->employeeContractService->getEmployeeWithActiveType($date, $employeeTypeId, $functionId);
        foreach ($employeeContracts as $employeeContract) {
            $employeeDetails = [];
            $employeeDetails['id'] = $employeeContract->employeeProfile->id;
            $employeeDetails['name'] = $employeeContract->employeeProfile->user->userBasicDetails->first_name . ' ' .
                $employeeContract->employeeProfile->user->userBasicDetails->last_name;
            $plans = $employeeContract->employeeProfile->planningsForDate($date);
            $plannings = [];
            foreach ($plans as $plan) {
                $plannings[] = [
                    'plan_start_time' => date('H:i', strtotime($plan->start_date_time)),
                    'plan_end_time'   => date('H:i', strtotime($plan->end_date_time))
                ];
            }
            $employeeDetails['plannings'] = $plannings;
        }
        return $employeeDetails;
    }
    public function getDayPlansManager($values)
    {
        $response = [];
        $plans = $this->planningRepository->getPlansBetweenDates($values['location_id'], [], '', $values['date'], $values['date'], '', [
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
        foreach ($plans as $plan) {
            $startPlan = $stopPlan = false;
            if (strtotime($plan->start_date_time) <= strtotime(date('Y-m-d H:i')) && strtotime($plan->end_date_time) >= strtotime(date('Y-m-d H:i'))) {
                if ($plan->plan_started) {
                    $startPlan = false;
                    $stopPlan = true;
                } else {
                    $startPlan = true;
                    $stopPlan = false;
                }
            }
            $response[] = [
                'start_time'    => date('H:i', strtotime($plan->start_date_time)),
                'end_time'      => date('H:i', strtotime($plan->end_date_time)),
                'employee_type' => $plan->employeeType->name,
                'function'      => $plan->functionTitle->name,
                'workstation'   => $plan->workstation->workstation_name,
                'start_plan'    => $startPlan,
                'stop_plan'     => $stopPlan,
            ];
        }
        return $response;
    }
}
