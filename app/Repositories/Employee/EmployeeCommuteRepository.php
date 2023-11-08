<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeCommuteRepositoryInterface;
use App\Models\Employee\EmployeeCommute;

class EmployeeCommuteRepository implements EmployeeCommuteRepositoryInterface
{

    public function getEmployeeCommuteById(string $employeeCommuteId): EmployeeCommute
    {
        return EmployeeCommute::findOrFail($employeeCommuteId);
    }

    public function deleteEmployeeCommute(string $employeeCommuteId): bool
    {
        return EmployeeCommute::destroy($employeeCommuteId);
    }

    public function createEmployeeCommute(array $extraBenefitsDetails): EmployeeCommute
    {
        return EmployeeCommute::create($extraBenefitsDetails);
    }

    public function updateEmployeeCommute(string $employeeCommuteId, array $newDetails): bool
    {
        return EmployeeCommute::whereId($employeeCommuteId)->update($newDetails);
    }
}
