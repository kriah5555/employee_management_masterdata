<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeFunctionDetailsRepositoryInterface;
use App\Models\Company\Employee\EmployeeFunctionDetails;

class EmployeeFunctionDetailsRepository implements EmployeeFunctionDetailsRepositoryInterface
{

    public function getEmployeeFunctionDetailsById(string $employeeFunctionDetailsId): EmployeeFunctionDetails
    {
        return EmployeeFunctionDetails::findOrFail($employeeFunctionDetailsId);
    }

    public function deleteEmployeeFunctionDetails(string $employeeFunctionDetailsId): bool
    {
        return EmployeeFunctionDetails::destroy($employeeFunctionDetailsId);
    }

    public function createEmployeeFunctionDetails(array $extraBenefitsDetails): EmployeeFunctionDetails
    {
        return EmployeeFunctionDetails::create($extraBenefitsDetails);
    }

    public function updateEmployeeFunctionDetails(string $employeeFunctionDetailsId, array $newDetails): bool
    {
        return EmployeeFunctionDetails::whereId($employeeFunctionDetailsId)->update($newDetails);
    }
}
