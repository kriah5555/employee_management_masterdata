<?php

namespace App\Repositories\Contract;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\Contract\ContractTypeRepositoryInterface;
use App\Models\Contract\ContractType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class ContractTypeRepository implements ContractTypeRepositoryInterface
{
    public function getContractTypes(): Collection
    {
        return ContractType::all();
    }
    public function getActiveContractTypes(): Collection
    {
        return ContractType::where('status', '=', true)->get();
    }

    public function getContractTypeById(string $contractTypeId, array $relations = []): Collection|Builder|ContractType
    {
        return ContractType::with($relations)->findOrFail($contractTypeId);
    }

    public function deleteContractType(ContractType $contractType): bool
    {
        if ($contractType->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete contract type');
        }
    }

    public function createContractType(array $details): ContractType
    {
        return ContractType::create($details);
    }

    public function updateContractType(ContractType $contractType, array $updatedDetails): bool
    {
        if ($contractType->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update contract type');
        }
    }
}