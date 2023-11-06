<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeSocialSecretaryDetailsRepositoryInterface;
use App\Models\Employee\EmployeeBenefits;

class EmployeeSocialSecretaryDetailsRepository implements EmployeeSocialSecretaryDetailsRepositoryInterface
{

    public function getEmployeeSocialSecretaryDetailsById(string $employeeSocialSecretaryDetailsId): EmployeeBenefits
    {
        return EmployeeBenefits::findOrFail($employeeSocialSecretaryDetailsId);
    }

    public function deleteEmployeeSocialSecretaryDetails(string $employeeSocialSecretaryDetailsId)
    {
        EmployeeBenefits::destroy($employeeSocialSecretaryDetailsId);
    }

    public function createEmployeeSocialSecretaryDetails(array $employeeSocialSecretaryDetailsDetails): EmployeeBenefits
    {
        return EmployeeBenefits::create($employeeSocialSecretaryDetailsDetails);
    }

    public function updateEmployeeSocialSecretaryDetails(string $employeeSocialSecretaryDetailsId, array $newDetails)
    {
        return EmployeeBenefits::whereId($employeeSocialSecretaryDetailsId)->update($newDetails);
    }
}
