<?php

namespace App\Interfaces\EmployeeType;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\EmployeeType\EmployeeTypeDimonaConfig;

interface EmployeeTypeDimonaConfigRepositoryInterface
{
    public function getEmployeeTypeDimonaConfigById(string $employeeTypeDimonaConfigId, array $relations = []): Collection|Builder|EmployeeTypeDimonaConfig;

    public function createEmployeeTypeDimonaConfig(array $details): EmployeeTypeDimonaConfig;

    public function updateEmployeeTypeDimonaConfig(EmployeeTypeDimonaConfig $employeeTypeDimonaConfig, array $updatedDetails): bool;
}