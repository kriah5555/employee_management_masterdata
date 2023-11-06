<?php

namespace App\Interfaces\Employee;

use App\Models\Employee\EmployeeBenefits;

interface EmployeeBenefitsRepositoryInterface
{

    public function getEmployeeBenefitsById(string $employeeSocialSecretaryDetailsId): EmployeeBenefits;

    public function deleteEmployeeBenefits(string $employeeSocialSecretaryDetailsId): bool;

    public function createEmployeeBenefits(array $employeeSocialSecretaryDetailsDetails): EmployeeBenefits;

    public function updateEmployeeBenefits(string $employeeSocialSecretaryDetailsId, array $newDetails): bool;
}
