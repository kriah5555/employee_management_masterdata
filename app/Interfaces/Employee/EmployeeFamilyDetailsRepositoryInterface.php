<?php

namespace App\Interfaces\Employee;

interface EmployeeFamilyDetailsRepositoryInterface
{

    public function getEmployeeFamilyDetailsById(string $id);

    public function deleteEmployeeFamilyDetails(string $id);

    public function createEmployeeFamilyDetails(array $details);

    public function updateEmployeeFamilyDetails(string $id, array $updatedDetails);
}
