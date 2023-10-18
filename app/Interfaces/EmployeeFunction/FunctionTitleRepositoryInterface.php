<?php

namespace App\Interfaces\EmployeeFunction;

use App\Models\EmployeeFunction\FunctionTitle;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface FunctionTitleRepositoryInterface
{
    public function getFunctionTitles(): Collection;

    public function getActiveFunctionTitles(): Collection;

    public function getFunctionTitleById(string $functionTitleId, array $relations = []): Collection|Builder|FunctionTitle;

    public function deleteFunctionTitle(FunctionTitle $functionTitle): bool;

    public function createFunctionTitle(array $details): FunctionTitle;

    public function updateFunctionTitle(FunctionTitle $functionTitle, array $updatedDetails): bool;
}