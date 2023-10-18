<?php

namespace App\Interfaces;

interface EmployeeProfileRepositoryInterface
{

    public function getEmployeeProfileById(string $id);

    public function deleteEmployeeProfile(string $id);

    public function createEmployeeProfile(array $details);

    public function updateEmployeeProfile(string $id, array $updatedDetails);
}