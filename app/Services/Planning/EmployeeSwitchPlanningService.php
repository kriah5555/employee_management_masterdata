<?php

namespace App\Services\Planning;

use Illuminate\Support\Facades\DB;
use App\Models\Planning\EmployeeSwitchPlanning;
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
            $activeEmployees[$contract->employeeProfile->id] = [
                'value' => $contract->employeeProfile->id,
                'label' => $contract->employeeProfile->user->userBasicDetails->first_name . ' ' . $contract->employeeProfile->user->userBasicDetails->last_name
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
}
