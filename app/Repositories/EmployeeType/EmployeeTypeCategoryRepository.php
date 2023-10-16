<?php

namespace App\Repositories\EmployeeType;

use App\Interfaces\EmployeeType\EmployeeTypeCategoryRepositoryInterface;
use App\Models\EmployeeType\EmployeeTypeCategory;

class EmployeeTypeCategoryRepository implements EmployeeTypeCategoryRepositoryInterface
{
    public function getEmployeeTypeCategoryById(string $employeeTypeCategoryId, array $relations = [])
    {
        return EmployeeTypeCategory::with($relations)->findOrFail($employeeTypeCategoryId);
    }

    public function getEmployeeTypeCategories()
    {
        return EmployeeTypeCategory::all();
    }

    public function getActiveEmployeeTypeCategories()
    {
        return EmployeeTypeCategory::where('status', true)->get();
    }
}