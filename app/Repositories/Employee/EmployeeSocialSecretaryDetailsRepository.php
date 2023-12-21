<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeSocialSecretaryDetailsRepositoryInterface;
use App\Models\Company\Employee\EmployeeSocialSecretaryDetails;

class EmployeeSocialSecretaryDetailsRepository implements EmployeeSocialSecretaryDetailsRepositoryInterface
{

    public function getEmployeeSocialSecretaryDetailsById(string $employeeSocialSecretaryDetailsId): EmployeeSocialSecretaryDetails
    {
        return EmployeeSocialSecretaryDetails::findOrFail($employeeSocialSecretaryDetailsId);
    }

    public function deleteEmployeeSocialSecretaryDetails(string $employeeSocialSecretaryDetailsId)
    {
        EmployeeSocialSecretaryDetails::destroy($employeeSocialSecretaryDetailsId);
    }

    public function createEmployeeSocialSecretaryDetails(array $employeeSocialSecretaryDetailsDetails): EmployeeSocialSecretaryDetails
    {
        return EmployeeSocialSecretaryDetails::create($employeeSocialSecretaryDetailsDetails);
    }

    public function updateEmployeeSocialSecretaryDetails(string $employeeSocialSecretaryDetailsId, array $newDetails)
    {
        return EmployeeSocialSecretaryDetails::whereId($employeeSocialSecretaryDetailsId)->update($newDetails);
    }
}
