<?php

namespace App\Services\Contract;

use App\Models\Contract\ContractType;
use App\Repositories\Contract\ContractTypeRepository;
use App\Exceptions\ModelUpdateFailedException;
use App\Exceptions\ModelDeleteFailedException;

class ContractTypeService
{
    protected $contractTypeRepository;

    public function __construct(ContractTypeRepository $contractTypeRepository)
    {
        $this->contractTypeRepository = $contractTypeRepository;
    }
    public function getContractTypes()
    {
        return $this->contractTypeRepository->getContractTypes();
    }

    public function createContractType($values)
    {
        return $this->contractTypeRepository->createContractType($values);
    }

    public function getContractTypeDetails($id)
    {
        return $this->contractTypeRepository->getContractTypeById($id, ['contractRenewalType']);
    }

    public function updateContractType(ContractType $contractType, $values)
    {
        return $this->contractTypeRepository->updateContractType($contractType, $values);
    }
    public function deleteContractType(ContractType $contractType)
    {
        return $this->contractTypeRepository->deleteContractType($contractType);
    }
    public function getActiveContractTypes()
    {
        return $this->contractTypeRepository->getActiveContractTypes();
    }
}