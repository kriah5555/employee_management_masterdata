<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\LongTermEmployeeContractRepositoryInterface;
use App\Models\Company\Employee\LongTermEmployeeContract;

class LongTermEmployeeContractRepository implements LongTermEmployeeContractRepositoryInterface
{

    public function getLongTermEmployeeContractById(string $longTermEmployeeContractId): LongTermEmployeeContract
    {
        return LongTermEmployeeContract::findOrFail($longTermEmployeeContractId);
    }

    public function deleteLongTermEmployeeContract(string $longTermEmployeeContractId): bool
    {
        return LongTermEmployeeContract::destroy($longTermEmployeeContractId);
    }

    public function createLongTermEmployeeContract(array $extraBenefitsDetails): LongTermEmployeeContract
    {
        return LongTermEmployeeContract::create($extraBenefitsDetails);
    }

    public function updateLongTermEmployeeContract(string $longTermEmployeeContractId, array $newDetails): bool
    {
        return LongTermEmployeeContract::whereId($longTermEmployeeContractId)->update($newDetails);
    }
}
