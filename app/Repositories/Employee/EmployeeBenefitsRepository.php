<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeBenefitsRepositoryInterface;
use App\Models\Company\Employee\EmployeeBenefits;

class EmployeeBenefitsRepository implements EmployeeBenefitsRepositoryInterface
{

    public function getEmployeeBenefitsById(string $employeeBenefitsId): EmployeeBenefits
    {
        return EmployeeBenefits::findOrFail($employeeBenefitsId);
    }

    public function deleteEmployeeBenefits(string $employeeBenefitsId): bool
    {
        return EmployeeBenefits::destroy($employeeBenefitsId);
    }

    public function createEmployeeBenefits(array $extraBenefitsDetails): EmployeeBenefits
    {
        return EmployeeBenefits::create($extraBenefitsDetails);
    }

    public function updateEmployeeBenefits(string $employeeBenefitsId, array $newDetails): bool
    {
        return EmployeeBenefits::whereId($employeeBenefitsId)->update($newDetails);
    }
}
