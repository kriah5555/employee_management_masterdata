<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBase;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ModelDeleteFailedException;
use App\Models\Planning\TimeRegistration;

class PlanningStartStopService
{
    public function __construct(
        protected PlanningRepository $planningRepository,
        protected EmployeeService $employeeService
    ) {
    }

    public function getEmployeePlanningCreateOptions($values)
    {
        $date = date('Y-m-d', strtotime($values['date']));
        return $this->employeeService->getEmployeeActiveTypesByDate($values['employee_id'], $date);
    }

    public function startPlanByManager($values)
    {
        DB::connection('tenant')->beginTransaction();
        $plan = $this->planningRepository->getPlanningById($values['plan_id']);
        $plan->plan_started = true;
        $plan->save();
        TimeRegistration::create([
            'plan_id'           => $plan->id,
            'actual_start_time' => date('Y-m-d H:i', strtotime($values['start_time'])),
            'status'            => true,
        ]);
        DB::connection('tenant')->commit();
    }
    public function stopPlanByManager($values)
    {
        DB::connection('tenant')->beginTransaction();
        $plan = $this->planningRepository->getPlanningById($values['plan_id']);
        $plan->plan_started = false;
        $plan->save();
        $timeRegistration = $plan->timeRegistrations->last();
        $timeRegistration->actual_end_time = date('Y-m-d H:i', strtotime($values['stop_time']));
        $timeRegistration->save();
        DB::connection('tenant')->commit();
    }

}
