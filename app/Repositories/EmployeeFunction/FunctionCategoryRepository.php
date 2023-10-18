<?php

namespace App\Repositories\EmployeeFunction;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\EmployeeFunction\FunctionCategoryRepositoryInterface;
use App\Models\EmployeeFunction\FunctionCategory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FunctionCategoryRepository implements FunctionCategoryRepositoryInterface
{
    public function getFunctionCategories(): Collection
    {
        return FunctionCategory::all();
    }
    public function getActiveFunctionCategories(): Collection
    {
        return FunctionCategory::where('status', '=', true)->get();
    }

    public function getFunctionCategoryById(string $functionCategoryId, array $relations = []): Collection|Builder|FunctionCategory
    {
        return FunctionCategory::with($relations)->findOrFail($functionCategoryId);
    }

    public function deleteFunctionCategory(FunctionCategory $functionCategory): bool
    {
        if ($functionCategory->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete function category');
        }
    }

    public function createFunctionCategory(array $details): FunctionCategory
    {
        return FunctionCategory::create($details);
    }

    public function updateFunctionCategory(FunctionCategory $functionCategory, array $updatedDetails): bool
    {
        if ($functionCategory->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update function category');
        }
    }
}