<?php

namespace App\Interfaces;

interface EmployeeCommuteRepositoryInterface
{

    public function getEmployeeCommuteById(string $id);

    public function deleteEmployeeCommute(string $id);

    public function createEmployeeCommute(array $details);

    public function updateEmployeeCommute(string $id, array $updatedDetails);
}
