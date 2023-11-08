<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeContractRepositoryInterface;
use App\Models\Employee\EmployeeContract;

class EmployeeContractRepository implements EmployeeContractRepositoryInterface
{

    public function getEmployeeContractById(string $employeeContractId): EmployeeContract
    {
        return EmployeeContract::findOrFail($employeeContractId);
    }

    public function deleteEmployeeContract(string $employeeContractId): bool
    {
        return EmployeeContract::destroy($employeeContractId);
    }

    public function createEmployeeContract(array $extraBenefitsDetails): EmployeeContract
    {
        return EmployeeContract::create($extraBenefitsDetails);
    }

    public function updateEmployeeContract(string $employeeContractId, array $newDetails): bool
    {
        return EmployeeContract::whereId($employeeContractId)->update($newDetails);
    }
}
