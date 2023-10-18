<?php

namespace App\Repositories\EmployeeFunction;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\EmployeeFunction\FunctionTitleRepositoryInterface;
use App\Models\EmployeeFunction\FunctionTitle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class FunctionTitleRepository implements FunctionTitleRepositoryInterface
{
    public function getFunctionTitles(): Collection
    {
        return FunctionTitle::all();
    }
    public function getActiveFunctionTitles(): Collection
    {
        return FunctionTitle::where('status', '=', true)->get();
    }

    public function getFunctionTitleById(string $functionTitleId, array $relations = []): Collection|Builder|FunctionTitle
    {
        return FunctionTitle::with($relations)->findOrFail($functionTitleId);
    }

    public function deleteFunctionTitle(FunctionTitle $functionTitle): bool
    {
        if ($functionTitle->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete function');
        }
    }

    public function createFunctionTitle(array $details): FunctionTitle
    {
        return FunctionTitle::create($details);
    }

    public function updateFunctionTitle(FunctionTitle $functionTitle, array $updatedDetails): bool
    {
        if ($functionTitle->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update function');
        }
    }
}