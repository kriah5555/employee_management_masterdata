<?php

namespace App\Interfaces\Employee;

use App\Models\Company\Employee\EmployeeCommute;

interface EmployeeCommuteRepositoryInterface
{

    public function getEmployeeCommuteById(string $employeeSocialSecretaryDetailsId): EmployeeCommute;

    public function deleteEmployeeCommute(string $employeeSocialSecretaryDetailsId): bool;

    public function createEmployeeCommute(array $employeeSocialSecretaryDetailsDetails): EmployeeCommute;

    public function updateEmployeeCommute(string $employeeSocialSecretaryDetailsId, array $newDetails): bool;
}
