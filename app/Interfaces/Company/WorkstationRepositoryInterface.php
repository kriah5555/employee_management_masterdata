<?php

namespace App\Interfaces\Company;

interface WorkstationRepositoryInterface
{
    public function getWorkstationsOfCompany($companyId);

    public function getActiveWorkstationsOfCompany($companyId);

    public function getWorkstationById(string $workstationId);

    public function deleteWorkstation(string $workstationId);

    public function createWorkstation(array $details);

    public function updateWorkstation(string $workstationId, array $updatedDetails);
}