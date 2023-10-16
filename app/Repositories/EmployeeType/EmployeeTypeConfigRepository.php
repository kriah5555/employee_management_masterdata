<?php

namespace App\Repositories\EmployeeType;

use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\EmployeeType\EmployeeTypeConfigRepositoryInterface;
use App\Models\EmployeeType\EmployeeTypeConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EmployeeTypeConfigRepository implements EmployeeTypeConfigRepositoryInterface
{
    public function getEmployeeTypeConfigById(string $employeeTypeConfigId, array $relations = []): Collection|Builder|EmployeeTypeConfig
    {
        return EmployeeTypeConfig::with($relations)->findOrFail($employeeTypeConfigId);
    }

    public function createEmployeeTypeConfig(array $details): EmployeeTypeConfig
    {
        return EmployeeTypeConfig::create($details);
    }

    public function updateEmployeeTypeConfig(EmployeeTypeConfig $employeeTypeConfig, array $updatedDetails): bool
    {
        if ($employeeTypeConfig->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update employee type config');
        }
    }
}