<?php

namespace App\Services\Planning;

use Illuminate\Support\Facades\DB;
use App\Models\Planning\EmployeeSwitchPlanning;
use App\Repositories\Company\CompanyRepository;
use App\Repositories\Planning\PlanningRepository;
use App\Models\Company\Employee\EmployeeContract;

class EmployeeSwitchPlanningService
{
    public function __construct(
        protected PlanningRepository $planningRepository
    )
    {}

    public function getEmployeesToSwitchPlan($plan_id) # will get all the employee having active contract and with same employee types
    {
        $plan      = $this->planningRepository->getPlanningById($plan_id);
        $plan_date = date('Y-m-d', strtotime($plan->start_date_time));

        $contracts = EmployeeContract::with('employeeProfile.user.userBasicDetails')
                    ->where(function ($query) use ($plan_date) {
                        $query->where('end_date', '<=', $plan_date)
                            ->orWhereNull('end_date');
                            
                    })
                    ->where('employee_type_id', $plan->employee_type_id)
                    ->where('employee_profile_id', '!=', $plan->employee_profile_id)
                    ->get();

        $activeEmployees = [];
        foreach ($contracts as $contract) {
            $plans = $this->planningRepository->getPlans('', '', '', '', [$plan->employee_type_id], $plan->employee_profile_id, [], $plan->start_date_time, $plan->end_date_time);
            $plans = $plans->map(function ($plan) {
                return [
                    'start_time' => $plan->start_time,
                    'end_time'   => $plan->end_time,
                ];
            });
            $activeEmployees[$contract->employeeProfile->id] = [
                'employee_id'         => $contract->employeeProfile->id,
                'employee_name'       => $contract->employeeProfile->full_name,
                'availability_status' => $contract->employeeProfile->availabilityForDate($plan_date)->isNotEmpty() ? $contract->employeeProfile->availabilityForDate($plan_date)->first()->availability : null,
                'plan_timings'        => $plans,
            ];
        }
        
        $activeEmployees = array_values($activeEmployees);
        usort($activeEmployees, function ($a, $b) {
            return strcmp($a['label'], $b['label']);
        });
        return $activeEmployees;    
    }

    public function createSwitchPlanRequest($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $values['request_status'] = config('constants.SWITCH_PLAN_PENDING');
            EmployeeSwitchPlanning::create($values);
            DB::connection('tenant')->commit();
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllSwitchPlanRequests($user_id, $company_ids)
    {
        try {
            $return      = [
                'requests' => null,
                'accepted' => null,
            ];
            $company_repository = app(CompanyRepository::class);
            foreach ($company_ids as $company_id) {
                setTenantDBByCompanyId($company_id);
                $company = $company_repository->getCompanyById($company_id);
                $employee_profile_id  = getEmployeeProfileByUserId($user_id);
                $switch_plan_requests = EmployeeSwitchPlanning::where(['request_from' => $employee_profile_id, 'request_to' => $employee_profile_id])->get();
                $switch_plan_requests->each(function($switch_plan_request) use (&$return, $company) {
                    $request_data = [
                        'id' => $switch_plan_request->id,
                        'company_id' => $company->id,
                        'company_name' => $company->company_name,
                        'company_name' => $company->company_name,
                    ];
                });
            }

            EmployeeSwitchPlanning::create($values);
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
