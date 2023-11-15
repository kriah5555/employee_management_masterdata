<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeFamilyDetailsRepositoryInterface;
use App\Models\Company\Employee\EmployeeFamilyDetails;

class EmployeeFamilyDetailsRepository implements EmployeeFamilyDetailsRepositoryInterface
{

    public function getEmployeeFamilyDetailsById(string $employeeFamilyDetailsId): EmployeeFamilyDetails
    {
        return EmployeeFamilyDetails::findOrFail($employeeFamilyDetailsId);
    }

    public function deleteEmployeeFamilyDetails(string $employeeFamilyDetailsId): bool
    {
        return EmployeeFamilyDetails::destroy($employeeFamilyDetailsId);
    }

    public function createEmployeeFamilyDetails(array $extraBenefitsDetails): EmployeeFamilyDetails
    {
        return EmployeeFamilyDetails::create($extraBenefitsDetails);
    }

    public function updateEmployeeFamilyDetails(string $employeeFamilyDetailsId, array $newDetails): bool
    {
        return EmployeeFamilyDetails::whereId($employeeFamilyDetailsId)->update($newDetails);
    }
}
