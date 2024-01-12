<?php

namespace App\Repositories\EmployeeType;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\EmployeeType\EmployeeTypeRepositoryInterface;
use App\Models\EmployeeType\EmployeeType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Company\Company;

class EmployeeTypeRepository implements EmployeeTypeRepositoryInterface
{
    public function getEmployeeTypes(): Collection
    {
        return EmployeeType::all();
    }
    public function getActiveEmployeeTypes(): Collection
    {
        return EmployeeType::where('status', '=', true)->get();
    }

    public function getEmployeeTypeById(string $employeeTypeId, array $relations = []): Collection|Builder|EmployeeType
    {
        return EmployeeType::with($relations)->findOrFail($employeeTypeId);
    }

    public function deleteEmployeeType(EmployeeType $employeeType): bool
    {
        if ($employeeType->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete employee type');
        }
    }

    public function createEmployeeType(array $details): EmployeeType
    {
        return EmployeeType::create($details);
    }

    public function updateEmployeeType(EmployeeType $employeeType, array $updatedDetails): bool
    {
        if ($employeeType->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update employee type');
        }
    }
    public function updateEmployeeTypeContractTypes(EmployeeType $employeeType, array $contractTypes)
    {
        return $employeeType->contractTypes()->sync($contractTypes ?? []);
    }

    public function getCompanyEmployeeTypes($company_id)
    {
        $company = Company::findOrFail($company_id);
        $employeeTypes = [];
        foreach ($company->sectors as $sector) {
            foreach ($sector->employeeTypes as $employeeType) {
                $employeeTypes[$employeeType->id] = $employeeType;
            }
        }

        return array_values($employeeTypes);
    }
}
