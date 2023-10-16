<?php

namespace App\Repositories\Sector;

use App\Interfaces\Sector\SectorSalaryStepsRepositoryInterface;
use App\Models\Sector\SectorSalarySteps;
use App\Exceptions\ModelUpdateFailedException;

class SectorSalaryStepsRepository implements SectorSalaryStepsRepositoryInterface
{

    public function getOrCreateSectorSalarySteps($sectorSalaryConfigId): SectorSalarySteps
    {
        return SectorSalarySteps::firstOrCreate(['sector_salary_config_id' => $sectorSalaryConfigId]);
    }
    public function updateSectorSalarySteps(SectorSalarySteps $sectorSalaryStep, array $updatedDetails): bool
    {
        if ($sectorSalaryStep->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update sector salary step');
        }
    }
}