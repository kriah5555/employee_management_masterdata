<?php

namespace App\Interfaces\Employee;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Employee\EmployeeProfile;

interface EmployeeProfileRepositoryInterface
{

    public function getEmployeeProfileById(string $id, array $relations = []): Collection|Builder|EmployeeProfile;

    public function deleteEmployeeProfile(string $id);

    public function createEmployeeProfile(array $details);

    public function updateEmployeeProfile(string $id, array $updatedDetails);
}
