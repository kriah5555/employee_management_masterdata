<?php

namespace App\Repositories\Contract;

use App\Interfaces\Contract\ContractRenewalTypeRepositoryInterface;
use App\Models\Contract\ContractRenewalType;
use Illuminate\Support\Collection;

class ContractRenewalTypeRepository implements ContractRenewalTypeRepositoryInterface
{
    public function getActiveContractRenewalTypes(): Collection
    {
        return ContractRenewalType::where('status', '=', true)->get();
    }
}