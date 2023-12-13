<?php

namespace App\Services\Planning;

use App\Interfaces\Planning\PlanningCreateEditInterface;
use App\Services\Employee\EmployeeService;

class PlanningCreateEditService implements PlanningCreateEditInterface
{
    public function __construct(protected EmployeeService $employeeService)
    {
    }

    public function getEmployeePlanningCreateOptions($values)
    {
        $date = date('Y-m-d', strtotime($values['date']));
        return $this->employeeService->getEmployeeActiveTypesByDate($values['employee_id'], $date);
    }
}
