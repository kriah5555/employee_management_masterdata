<?php

namespace App\Interfaces\Contract;

use Illuminate\Support\Collection;

interface ContractRenewalTypeRepositoryInterface
{
    public function getActiveContractRenewalTypes(): Collection;
}