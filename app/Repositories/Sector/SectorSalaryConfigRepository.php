<?php

namespace App\Repositories\Sector;

use App\Interfaces\Sector\SectorSalaryConfigRepositoryInterface;
use App\Models\Sector\SectorSalaryConfig;
use App\Exceptions\ModelUpdateFailedException;

class SectorSalaryConfigRepository implements SectorSalaryConfigRepositoryInterface
{

    public function getOrCreateSectorSalaryConfig($sectorId): SectorSalaryConfig
    {
        return SectorSalaryConfig::firstOrCreate(['sector_id' => $sectorId]);
    }
    public function updateSectorSalaryConfig(SectorSalaryConfig $sectorSalaryConfig, array $updatedDetails): bool
    {
        if ($sectorSalaryConfig->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update employee type');
        }
    }
}