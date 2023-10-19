<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\WorkstationRepositoryInterface;
use App\Models\Workstation;

class WorkstationRepository implements WorkstationRepositoryInterface
{
    public function getWorkstationsOfCompany($companyId)
    {
        return Workstation::where('company_id', '=', $companyId)->get();
    }
    public function getActiveWorkstationsOfCompany($companyId)
    {
        return Workstation::where('company_id', '=', $companyId)->where('status', '=', true)->get();
    }

    public function getWorkstationById(string $workstationId): Workstation
    {
        return Workstation::findOrFail($workstationId);
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
}