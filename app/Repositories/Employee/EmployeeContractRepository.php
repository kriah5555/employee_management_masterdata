<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeContractRepositoryInterface;
use App\Models\Company\Employee\EmployeeContract;

class EmployeeContractRepository implements EmployeeContractRepositoryInterface
{

    public function getEmployeeContractById(string $employeeContractId): EmployeeContract
    {
        return EmployeeContract::findOrFail($employeeContractId);
    }

    public function deleteEmployeeContract(string $employeeContractId): bool
    {

        $employeeContract = EmployeeContract::findOrFail($employeeContractId);

        $employeeContract->employeeFunctionDetails()->delete();
        
        $employeeContract->employeeFunctionDetails()->delete();

        return $employeeContract->delete();
    }

    public function createEmployeeContract(array $extraBenefitsDetails): EmployeeContract
    {
        if (isset($extraBenefitsDetails['start_date']) && !empty($extraBenefitsDetails['start_date']))  {
            $extraBenefitsDetails['start_date'] = date('Y-m-d', strtotime($extraBenefitsDetails['start_date']));
        }
        if (isset($extraBenefitsDetails['end_date']) && !empty($extraBenefitsDetails['end_date']))  {
            $extraBenefitsDetails['end_date'] = date('Y-m-d', strtotime($extraBenefitsDetails['end_date']));
        }
        return EmployeeContract::create($extraBenefitsDetails);
    }

    public function updateEmployeeContract(string $employeeContractId, array $newDetails): bool
    {
        if (isset($newDetails['start_date']) && !empty($newDetails['start_date']))  {
            $newDetails['start_date'] = date('Y-m-d', strtotime($newDetails['start_date']));
        }
        if (isset($newDetails['end_date']) && !empty($newDetails['end_date']))  {
            $newDetails['end_date'] = date('Y-m-d', strtotime($newDetails['end_date']));
        }
        return EmployeeContract::whereId($employeeContractId)->update($newDetails);
    }
}
