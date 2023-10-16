<?php

namespace App\Repositories\Sector;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\Sector\SectorRepositoryInterface;
use App\Models\Sector\Sector;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SectorRepository implements SectorRepositoryInterface
{
    public function getSectors(): Collection
    {
        return Sector::all();
    }
    public function getActiveSectors(): Collection
    {
        return Sector::where('status', '=', true)->get();
    }

    public function getSectorById(string $sectorId, array $relations = []): Collection|Builder|Sector
    {
        return Sector::with($relations)->findOrFail($sectorId);
    }

    public function deleteSector(Sector $sector): bool
    {
        if ($sector->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete employee type');
        }
    }

    public function createSector(array $details): Sector
    {
        return Sector::create($details);
    }

    public function updateSector(Sector $sector, array $updatedDetails): bool
    {
        if ($sector->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update sector');
        }
    }
    public function updateSectorEmployeeTypes(Sector $sector, array $employeeTypes)
    {
        return $sector->employeeTypes()->sync($employeeTypes ?? []);
    }
}