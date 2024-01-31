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

            $employee_switch_request = EmployeeSwitchPlanning::where(['request_to' => $contract->employeeProfile->id, 'plan_id' => $plan_id, 'status' => true])->get();
            $activeEmployees[$contract->employeeProfile->id] = [
                'employee_id'         => $contract->employeeProfile->id,
                'employee_name'       => $contract->employeeProfile->full_name,
                'availability_status' => $contract->employeeProfile->availabilityForDate($plan_date)->isNotEmpty() ? $contract->employeeProfile->availabilityForDate($plan_date)->first()->availability : null,
                'plan_timings'        => $plans,
                'request_status'      => $employee_switch_request->isNotEmpty(),
            ];
        }
        
        $activeEmployees = array_values($activeEmployees);
        usort($activeEmployees, function ($a, $b) {
            return strcmp($a['employee_name'], $b['employee_name']);
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

    public function getAllEmployeeRequestsForSwitchPlan($user_id, $company_ids = [])
    {
        try {
            $company_repository = app(CompanyRepository::class);
            foreach ($company_ids as $company_id) {
                setTenantDBByCompanyId($company_id);
                $company              = $company_repository->getCompanyById($company_id);
                $employee_profile_id  = getEmployeeProfileByUserId($user_id);
                $switch_plan_requests = EmployeeSwitchPlanning::where(['request_to' => $employee_profile_id->id])->get();
                return $switch_plan_requests->map(function($switch_plan_request) use ($company) {
                    return [
                        'id'               => $switch_plan_request->id,
                        'company_id'       => $company->id,
                        'company_name'     => $company->company_name,
                        'plan_id'          => $switch_plan_request->plan->id,
                        'plan_timings'     => $switch_plan_request->plan->start_time . '-' . $switch_plan_request->plan->end_time,
                        'plan_function'    => $switch_plan_request->plan->functionTitle->name,
                        'employee_type'    => $switch_plan_request->plan->employeeType->name,
                        'request_to'       => $switch_plan_request->requestTo->full_name,
                        'request_from'     => $switch_plan_request->requestFrom->full_name,
                        'location_id'      => $switch_plan_request->plan->location->id,
                        'location_name'    => $switch_plan_request->plan->location->location_name,
                        'workstation_id'   => $switch_plan_request->plan->workstation->id,
                        'workstation_name' => $switch_plan_request->plan->workstation->workstation_name,
                    ];
                });
            }

        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateStatusOfSwitchPlanning($values)
    {
        try {

        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
