<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\WorkstationRepositoryInterface;
use App\Models\Company\Workstation;

class WorkstationRepository implements WorkstationRepositoryInterface
{
    public function getWorkstationsOfCompany()
    {
        return Workstation::all();
    }
    public function getActiveWorkstationsOfCompany()
    {
        return Workstation::where('status', '=', true)->get();
    }

    public function getWorkstationById(string $workstationId): Workstation
    {
        return Workstation::with(['locations', 'functionTitles'])->findOrFail($workstationId);
    }

    public function deleteWorkstation(string $workstationId)
    {
        Workstation::destroy($workstationId);
    }

    public function createWorkstation(array $details): Workstation
    {
        return Workstation::create($details);
    }

    public function updateWorkstation(string $workstationId, array $updatedDetails)
    {
        return Workstation::whereId($workstationId)->update($updatedDetails);
    }

    public function getWorkstationsOfLocation(string $location_id)
    {
        return Workstation::WhereHA($workstationId)->update($updatedDetails);
    }
}