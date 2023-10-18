<?php

namespace App\Interfaces\EmployeeType;

interface EmployeeTypeCategoryRepositoryInterface
{
    public function getEmployeeTypeCategories();

    public function getActiveEmployeeTypeCategories();

    public function getEmployeeTypeCategoryById(string $employeeTypeCategoryId, array $relations = []);
}