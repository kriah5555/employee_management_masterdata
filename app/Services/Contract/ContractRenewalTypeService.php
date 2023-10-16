<?php

namespace App\Services\Contract;

use App\Repositories\Contract\ContractRenewalTypeRepository;

class ContractRenewalTypeService
{
    protected $contractRenewalTypeRepository;

    public function __construct(ContractRenewalTypeRepository $contractRenewalTypeRepository)
    {
        $this->contractRenewalTypeRepository = $contractRenewalTypeRepository;
    }
    public function getActiveContractRenewalTypes()
    {
        return $this->contractRenewalTypeRepository->getActiveContractRenewalTypes();
    }
}