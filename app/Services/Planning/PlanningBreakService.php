<?php

namespace App\Services\Planning;

use App\Models\Planning\PlanningBreak;
use App\Repositories\Planning\PlanningRepository;
use App\Services\Employee\EmployeeService;
use Illuminate\Support\Facades\DB;
use App\Models\Planning\TimeRegistration;
use App\Repositories\Employee\EmployeeProfileRepository;


class PlanningBreakService
{
    public function __construct(
        protected PlanningRepository $planningRepository,
        protected EmployeeService $employeeService
    ) {
    }

    public function startBreak($values)
    {
        $startTime = date('Y-m-d H:i', strtotime($values['start_time']));
        return PlanningBreak::create(
            [
                'plan_id'          => $values['pid'],
                'break_start_time' => $startTime,
                'started_by'       => $values['started_by'],
            ]
        );
    }

    public function stopBreak($values)
    {
        $endTime = date('Y-m-d H:i', strtotime($values['end_time']));
        $break = PlanningBreak::where('plan_id', $values['pid'])->get()->last();
        $break->ended_by = $values['ended_by'];
        $break->break_end_time = $endTime;
        $break->save();
    }
}
