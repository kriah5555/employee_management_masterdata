<?php

namespace App\Interfaces;

interface EmployeeProfileRepositoryInterface
{
    public function getAllEmployeeProfiles();

    public function getEmployeeProfileById(string $employeeProfileId);

    public function deleteEmployeeProfile(string $employeeProfileId);

    public function createEmployeeProfile(array $employeeProfileDetails);

    public function updateEmployeeProfile(string $employeeProfileId, array $newDetails);

    public function getAllEmployeeProfilesByCompany(string $companyId);

    public function getEmployeeProfileInCompanyBySsn(string $companyId, string $socialSecurityNumber);
    public function checkEmployeeExistsInCompany(string $companyId, string $socialSecurityNumber);
}
