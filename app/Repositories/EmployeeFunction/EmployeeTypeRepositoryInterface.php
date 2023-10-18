<?php

namespace App\Interfaces\EmployeeFunction;

use App\Models\EmployeeType\EmployeeType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface EmployeeTypeRepositoryInterface
{
    public function getEmployeeTypes(): Collection;

    public function getActiveEmployeeTypes(): Collection;

    public function getEmployeeTypeById(string $employeeTypeId, array $relations = []): Collection|Builder|EmployeeType;

    public function deleteEmployeeType(EmployeeType $employeeType): bool;

    public function createEmployeeType(array $details): EmployeeType;

    public function updateEmployeeType(EmployeeType $employeeType, array $updatedDetails): bool;

    public function updateEmployeeTypeContractTypes(EmployeeType $employeeType, array $contractTypes);
}