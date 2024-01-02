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
use App\Services\Planning\PlanningService;
use App\Repositories\Company\CompanyRepository;
use App\Models\Company\Employee\EmployeeProfile;
use App\Services\DateService;

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

        $dates = app(DateService::class)->getDatesArray(formatDate(reset($weekDates)), formatDate(end($weekDates)));

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
                $response = $this->formatWeeklyData($plans, $company, $response);
            }
        }

        return $response;
    }

    public function formatWeeklyData($plans, $company, $format = [])
    {
        $return = !empty($format) ? $format : [];
        $company_id = $company->id;
        $company_name = $company->company_name;

        foreach ($plans as $index => $plan) {
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

        return array_values($return);
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
                    $response = $this->formatWeeklyData($plans, $company, $response);
                }
            }
        }

        return $response;
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
}
