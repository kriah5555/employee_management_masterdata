<?php

namespace App\Services\Planning;

use App\Repositories\Planning\PlanningShiftsRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Exceptions\HttpResponseException;


class PlanningShiftsService
{

    public function __construct(
        protected PlanningShiftsRepository $planningShiftsRepository,
        protected EmployeeService $employeeService,
        protected PlanningCreateEditService $planningCreateEditService
    ) {
    }

    public function getPlanningShifts($locationId, $workstationId)
    {
        return $this->planningShiftsRepository->getPlanningShifts($locationId, $workstationId);
    }
    public function storePlanningShifts($values)
    {
        return DB::transaction(function () use ($values) {
            return $this->planningShiftsRepository->storePlanningShifts($values);
        });
    }

    public function createShiftPlan($values)
    {
        $date = date('Y-m-d', strtotime($values['date']));
        $active = $this->employeeService->getEmployeeActiveTypesByDate($values['employee_id'], $date);
        if (count($active['employee_types']) == 1 && count($active['functions'][$active['employee_types'][0]['value']]) == 1) {
            $values['employee_type_id'] = $active['employee_types'][0]['value'];
            $values['function_id'] = $active['functions'][$active['employee_types'][0]['value']][0]['value'];
        } else {
            throw new HttpResponseException(
                response()->json([
                    'success'      => false,
                    'plan_created' => false,
                    'message'      => [
                        'Multiple employee type/function present'
                    ]
                ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
            );
        }
        $shift = $this->planningShiftsRepository->getPlanningShiftById($values['shift_id']);
        $values['dates'] = [$values['date']];
        $values['timings'] = [
            [
                "start_time"     => $shift->start_time,
                "end_time"       => $shift->end_time,
                "contract_hours" => numericToEuropean($shift->contract_hours)
            ]
        ];
        $this->planningCreateEditService->savePlans($values);
    }
}
