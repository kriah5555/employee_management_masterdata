<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Interfaces\Planning\PlanningInterface;
use App\Services\Planning\PlanningService;
use App\Repositories\Company\CompanyRepository;
use App\Models\Company\Employee\EmployeeProfile;

class PlanningMobileService implements PlanningInterface
{

    public function __construct(
        protected PlanningService $planningService,
        protected CompanyRepository $companyRepository,
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

        return $response;
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
        $return = [
            'company_id'   => $company->id,
            'company_name' => $company->company_name,
            'total_hours'  => 0,
            'overtime'     => 0,
            'planned_hours'=> 0,
            'break'        => 0,
            'plans'        => [],
        ];

        foreach ($plans as $plan) {
            $time_registrations = $plan->timeRegistrations;
            $breaks = $plan->breaks;

            $return['plans'][$plan->plan_date][] = [
                'plan_id'                  => $plan->id,
                'plan_date'                => $plan->plan_date,
                'employee_profile_id'      => $plan->employee_profile_id,
                'employee_profile_id'      => $plan->employeeType->name,
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
                'worked_hours'             => ($time_registrations) ? formatToEuropeHours($time_registrations->flatten()->pluck('worked_hours')->sum()) : 0,
                'worked_hours_formatted'   => ($time_registrations) ? formatToEuropeHours($time_registrations->flatten()->pluck('worked_hours')->sum()) : 0,
                'break_hours'              => ($breaks) ? formatToEuropeHours($breaks->flatten()->pluck('break_hours')->sum()) : 0,
                'break_hours_formatted'    => ($breaks) ? formatToEuropeHours($breaks->flatten()->pluck('break_hours')->sum()) : 0,
            ];
        }

        $calculation = collect($return['plans']);

        $return['total_worked_hours']   = formatToEuropeHours($calculation->pluck('*.worked_hours')->flatten()->sum());
        $return['overtime']             = 0; 
        $return['total_contract_hours'] = formatToEuropeHours($calculation->pluck('*.contract_hours')->flatten()->sum());
        $return['break']                = $calculation->pluck('*.break_hours')->flatten()->sum();
        $return['plans']                = array_values($return['plans']);

        return $return;
    }
}
