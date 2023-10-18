<?php

namespace App\Repositories\EmployeeType;

use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\EmployeeType\EmployeeTypeDimonaConfigRepositoryInterface;
use App\Models\EmployeeType\EmployeeTypeDimonaConfig;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class EmployeeTypeDimonaConfigRepository implements EmployeeTypeDimonaConfigRepositoryInterface
{
    public function getEmployeeTypeDimonaConfigById(string $employeeTypeDimonaConfigConfigId, array $relations = []): Collection|Builder|EmployeeTypeDimonaConfig
    {
        return EmployeeTypeDimonaConfig::with($relations)->findOrFail($employeeTypeDimonaConfigConfigId);
    }

    public function createEmployeeTypeDimonaConfig(array $details): EmployeeTypeDimonaConfig
    {
        return EmployeeTypeDimonaConfig::create($details);
    }

    public function updateEmployeeTypeDimonaConfig(EmployeeTypeDimonaConfig $employeeTypeDimonaConfigConfig, array $updatedDetails): bool
    {
        if ($employeeTypeDimonaConfigConfig->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update employee type dimona config');
        }
    }
}