<?php

namespace App\Interfaces\Contract;

use App\Models\Contract\ContractType;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;

interface ContractTypeRepositoryInterface
{
    public function getContractTypes(): Collection;

    public function getActiveContractTypes(): Collection;

    public function createContractType(array $details): ContractType;

    public function getContractTypeById(string $contractTypeId, array $relations = []): Collection|Builder|ContractType;

    public function updateContractType(ContractType $contractType, array $updatedDetails): bool;

    public function deleteContractType(ContractType $contractType): bool;
}