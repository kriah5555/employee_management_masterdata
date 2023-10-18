<?php

namespace App\Interfaces\Sector;

use App\Models\Sector\SectorSalarySteps;

interface SectorSalaryStepsRepositoryInterface
{
    public function getOrCreateSectorSalarySteps($sectorId): SectorSalarySteps;

    public function updateSectorSalarySteps(SectorSalarySteps $sectorSalaryConfig, array $updatedDetails): bool;
}