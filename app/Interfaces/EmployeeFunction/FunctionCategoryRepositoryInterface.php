<?php

namespace App\Interfaces\EmployeeFunction;

use App\Models\EmployeeFunction\FunctionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface FunctionCategoryRepositoryInterface
{
    public function getFunctionCategories(): Collection;

    public function getActiveFunctionCategories(): Collection;

    public function getFunctionCategoryById(string $functionCategoryId, array $relations = []): Collection|Builder|FunctionCategory;

    public function deleteFunctionCategory(FunctionCategory $functionCategory): bool;

    public function createFunctionCategory(array $details): FunctionCategory;

    public function updateFunctionCategory(FunctionCategory $functionCategory, array $updatedDetails): bool;
}