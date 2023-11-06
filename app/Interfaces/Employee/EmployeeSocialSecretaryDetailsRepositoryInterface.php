<?php

namespace App\Interfaces\Employee;

interface EmployeeSocialSecretaryDetailsRepositoryInterface
{

    public function getEmployeeSocialSecretaryDetailsById(string $employeeSocialSecretaryDetailsId);

    public function deleteEmployeeSocialSecretaryDetails(string $employeeSocialSecretaryDetailsId);

    public function createEmployeeSocialSecretaryDetails(array $employeeSocialSecretaryDetailsDetails);

    public function updateEmployeeSocialSecretaryDetails(string $employeeSocialSecretaryDetailsId, array $newDetails);
}
