<?php

namespace App\Interfaces\Employee;

interface EmployeeFunctionDetailsRepositoryInterface
{

    public function getEmployeeFunctionDetailsById(string $id);

    public function deleteEmployeeFunctionDetails(string $id);

    public function createEmployeeFunctionDetails(array $details);

    public function updateEmployeeFunctionDetails(string $id, array $updatedDetails);
}
