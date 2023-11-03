<?php

namespace App\Repositories;

use App\Interfaces\EmployeeProfileRepositoryInterface;
use App\Models\Employee\EmployeeProfile;
use App\Models\User;

class EmployeeProfileRepository implements EmployeeProfileRepositoryInterface
{
    public function getAllEmployeeProfiles()
    {
        return EmployeeProfile::all();
    }

    public function getEmployeeProfileById(string $employeeProfileId): EmployeeProfile
    {
        return EmployeeProfile::findOrFail($employeeProfileId);
    }

    public function deleteEmployeeProfile(string $employeeProfileId)
    {
        EmployeeProfile::destroy($employeeProfileId);
    }

    public function createEmployeeProfile(array $employeeProfileDetails): EmployeeProfile
    {
        return EmployeeProfile::create($employeeProfileDetails);
    }

    public function updateEmployeeProfile(string $employeeProfileId, array $newDetails)
    {
        return EmployeeProfile::whereId($employeeProfileId)->update($newDetails);
    }

    public function getAllEmployeeProfilesByCompany(string $companyId)
    {
        return EmployeeProfile::where('company_id', '=', $companyId)->get();
    }

    public function getEmployeeProfileInCompanyBySsn(string $companyId, string $socialSecurityNumber)
    {
        return EmployeeProfile::where('company_id', '=', $companyId)
            ->where('social_security_number', '=', $socialSecurityNumber)->get();
    }

    public function checkEmployeeExistsInCompany(string $companyId, string $socialSecurityNumber)
    {
        // return User::where('social_security_number', $socialSecurityNumber)->whereHas('employeeProfiles', function ($query) use ($companyId) {
        //     $query->on('master')->where('company_id', $companyId);
        // })->exists();
        // return User::where('id', $userId)
        //     ->whereHas('employees', function ($query) use ($employeeId) {
        //         $query->where('id', $employeeId);
        //     })
        //     ->exists();
        return EmployeeProfile::whereHas('user', function ($query) use ($socialSecurityNumber) {
            $query->where('social_security_number', $socialSecurityNumber);
        })->exists();
        // return EmployeeProfile::where('company_id', '=', $companyId)
        //     ->where('social_security_number', '=', $socialSecurityNumber)->exists();
    }

    public function getEmployeeProfileBySsn(string $socialSecurityNumber)
    {
        return EmployeeProfile::whereRaw("REPLACE(REPLACE(social_security_number, '.', ''), '-', '') = ?", [$socialSecurityNumber])->get();
    }
}
