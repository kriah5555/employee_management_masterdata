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
            $plans = $this->planningRepository->getPlans('', '', '', '', [$plan->employee_type_id], $contract->employeeProfile->id, [], $plan->start_date_time, $plan->end_date_time);
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
                'request_status'      => $employee_switch_request->isNotEmpty(), # true for requested, false for not requested
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

    public function getAllEmployeeRequestsForSwitchPlan($user_id, $company_ids = [], $employee_flow = false)
    {
        try {
            if ($employee_flow) {
                return $this->getSwitchPlanRequestsForEmployeeFlow($user_id, $company_ids);
            } else {
                return $this->getAllSwitchPlanRequests();
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    
    private function getSwitchPlanRequestsForEmployeeFlow($user_id, $company_ids)
    {
        $switchPlanRequests = collect();
    
        $companyRepository = app(CompanyRepository::class);
    
        foreach ($company_ids as $company_id) {
            setTenantDBByCompanyId($company_id);
            $company = $companyRepository->getCompanyById($company_id);
    
            $employeeProfileId = getEmployeeProfileByUserId($user_id);
    
            $requests = EmployeeSwitchPlanning::where([
                'request_to' => $employeeProfileId->id,
                'status'     => true,
                'request_status' => config('constants.SWITCH_PLAN_PENDING'),
            ])->get();
    
            $switchPlanRequests = $switchPlanRequests->merge($this->mapSwitchPlanRequests($requests, $company));
        }
    
        return $switchPlanRequests;
    }
    
    private function getAllSwitchPlanRequests()
    {
        $switchPlanRequests = EmployeeSwitchPlanning::where(['status' => true, 'request_status' => config('constants.SWITCH_PLAN_PENDING')])->get();
    
        return $this->mapSwitchPlanRequests($switchPlanRequests);
    }
    
    private function mapSwitchPlanRequests($requests, $company = null)
    {
        return $requests->map(function ($switchPlanRequest) use ($company) {
            return [
                'id'               => $switchPlanRequest->id,
                'company_id'       => $company ? $company->id : null,
                'company_name'     => $company ? $company->company_name : null,
                'plan_id'          => $switchPlanRequest->plan->id,
                'plan_date'        => $switchPlanRequest->plan->plan_date ,
                'plan_timings'     => $switchPlanRequest->plan->start_time . '-' . $switchPlanRequest->plan->end_time,
                'plan_function_id' => $switchPlanRequest->plan->functionTitle->id,
                'plan_function'    => $switchPlanRequest->plan->functionTitle->name,
                'employee_type'    => $switchPlanRequest->plan->employeeType->name,
                'request_to'       => $switchPlanRequest->requestTo->full_name,
                'request_from'     => $switchPlanRequest->requestFrom->full_name,
                'location_id'      => $switchPlanRequest->plan->location->id,
                'location_name'    => $switchPlanRequest->plan->location->location_name,
                'workstation_id'   => $switchPlanRequest->plan->workstation->id,
                'workstation_name' => $switchPlanRequest->plan->workstation->workstation_name,
            ];
        });
    }

    public function updateStatusOfSwitchPlanning($values)
    {
        try {
            if (isset($values['company_id'])) {
                setTenantDBByCompanyId($values['company_id']);
            }

            DB::connection('tenant')->beginTransaction();

            $employeeSwitchPlanning = EmployeeSwitchPlanning::find($values['plan_switch_id']);
            $employeeSwitchPlanning->update(['request_status' => $values['status']]); # update the request status

            if ($values['status'] == config('constants.SWITCH_PLAN_APPROVE')) {
                $planDetails = $employeeSwitchPlanning->plan;
                $planDetails->update(['employee_profile_id' => $employeeSwitchPlanning->request_to]); # switch plan to employee

                EmployeeSwitchPlanning::where('plan_id', $employeeSwitchPlanning->plan_id)
                    ->where('request_to', '!=', $employeeSwitchPlanning->request_to)
                    ->update(['status' => false]); # update all other records status
            }

            DB::connection('tenant')->commit();
            return;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
