<?php

namespace App\Repositories;

use App\Interfaces\ExtraBenefitsRepositoryInterface;
use App\Models\Employee\EmployeeBenefits;

class ExtraBenefitsRepository implements ExtraBenefitsRepositoryInterface
{

    public function getExtraBenefitsById(string $extraBenefitsId): EmployeeBenefits
    {
        return EmployeeBenefits::findOrFail($extraBenefitsId);
    }

    public function deleteExtraBenefits(string $extraBenefitsId)
    {
        EmployeeBenefits::destroy($extraBenefitsId);
    }

    public function createExtraBenefits(array $extraBenefitsDetails): EmployeeBenefits
    {
        return EmployeeBenefits::create($extraBenefitsDetails);
    }

    public function updateExtraBenefits(string $extraBenefitsId, array $newDetails)
    {
        return EmployeeBenefits::whereId($extraBenefitsId)->update($newDetails);
    }
}