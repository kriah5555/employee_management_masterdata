<?php

namespace App\Interfaces\Company;

interface WorkstationRepositoryInterface
{
    public function getWorkstationsOfCompany();

    public function getActiveWorkstationsOfCompany();

    public function getWorkstationById(string $workstationId);

    public function deleteWorkstation(string $workstationId);

    public function createWorkstation(array $details);

    public function updateWorkstation(string $workstationId, array $updatedDetails);
}