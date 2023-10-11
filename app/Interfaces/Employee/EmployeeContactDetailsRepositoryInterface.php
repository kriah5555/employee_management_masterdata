<?php

namespace App\Interfaces;

interface EmployeeContactDetailsRepositoryInterface
{

    public function getEmployeeContactDetailsById(string $id);

    public function deleteEmployeeContactDetails(string $id);

    public function createEmployeeContactDetails(array $details);

    public function updateEmployeeContactDetails(string $id, array $updatedDetails);
}
