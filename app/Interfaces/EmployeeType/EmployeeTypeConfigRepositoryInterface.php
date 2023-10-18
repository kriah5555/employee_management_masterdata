<?php

namespace App\Interfaces\EmployeeType;

use App\Models\EmployeeType\EmployeeTypeConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface EmployeeTypeConfigRepositoryInterface
{

    public function getEmployeeTypeConfigById(string $employeeTypeConfigId, array $relations = []): Collection|Builder|EmployeeTypeConfig;

    public function createEmployeeTypeConfig(array $details): EmployeeTypeConfig;

    public function updateEmployeeTypeConfig(EmployeeTypeConfig $employeeTypeConfig, array $updatedDetails): bool;
}