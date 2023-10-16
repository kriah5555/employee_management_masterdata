<?php

namespace App\Interfaces\Sector;

use App\Models\Sector\Sector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface SectorRepositoryInterface
{
    public function getSectors(): Collection;

    public function getActiveSectors(): Collection;

    public function getSectorById(string $sectorId, array $relations = []): Collection|Builder|Sector;

    public function deleteSector(Sector $sector): bool;

    public function createSector(array $details): Sector;

    public function updateSector(Sector $sector, array $updatedDetails): bool;
}