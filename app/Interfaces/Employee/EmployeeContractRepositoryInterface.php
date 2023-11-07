<?php

namespace App\Interfaces\Employee;

interface EmployeeContractRepositoryInterface
{

    public function getEmployeeContractById(string $id);

    public function deleteEmployeeContract(string $id);

    public function createEmployeeContract(array $details);

    public function updateEmployeeContract(string $id, array $updatedDetails);
}
