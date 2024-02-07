<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBreak;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;
use App\Models\Planning\TimeRegistration;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Models\Planning\PlanningBase;

class PlanningBreakService
{
    public function __construct(
        protected PlanningRepository $planningRepository,
        protected EmployeeService $employeeService
    ) {
    }

    public function startBreak($values)
    {

        try {
            DB::connection('tenant')->beginTransaction();
            $startTime = date('Y-m-d H:i', strtotime($values['start_time']));
            PlanningBase::where('id', $values['plan_id'])->update(['break_started' => true]);
            $data = PlanningBreak::create(
                [
                    'plan_id'          => $values['plan_id'],
                    'break_start_time' => $startTime,
                    'started_by'       => $values['started_by'],
                ]
            );
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function stopBreak($values)
    {
        $endTime = date('Y-m-d H:i', strtotime($values['end_time']));
        PlanningBase::where('id', $values['plan_id'])->update(['break_started' => false]);
        $break = PlanningBreak::where('plan_id', $values['plan_id'])->get()->last();
        $break->ended_by = $values['ended_by'];
        $break->break_end_time = $endTime;
        $break->save();
    }
}
