<?php

namespace App\Interfaces\Sector;

use App\Models\Sector\SectorSalaryConfig;

interface SectorSalaryConfigRepositoryInterface
{
    public function getOrCreateSectorSalaryConfig($sectorId): SectorSalaryConfig;

    public function updateSectorSalaryConfig(SectorSalaryConfig $sectorSalaryConfig, array $updatedDetails): bool;
}