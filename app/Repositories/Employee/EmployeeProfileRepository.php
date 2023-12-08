<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeProfileRepositoryInterface;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User\User;

class EmployeeProfileRepository implements EmployeeProfileRepositoryInterface
{
    public function getAllEmployeeProfiles(array $relations = [])
    {
        return EmployeeProfile::with($relations)->get();
    }

    public function getEmployeeProfileById(string $id, array $relations = []): Collection|Builder|EmployeeProfile
    {
        return EmployeeProfile::with($relations)->findOrFail($id);
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

    public function checkEmployeeExistsInCompany($company_id, string $socialSecurityNumber)
    {
        $user = User::where('social_security_number', '=', $socialSecurityNumber)->get();
        if ($user->isNotEmpty()) {
            $userIds = $user->pluck('id')->toArray();
            if (EmployeeProfile::whereIn('user_id', $userIds)->get()->isNotEmpty()) {
                return true;
            }
        }
        return false;
    }

    public function getEmployeeProfileBySsn(string $socialSecurityNumber)
    {
        return EmployeeProfile::whereRaw("REPLACE(REPLACE(social_security_number, '.', ''), '-', '') = ?", [$socialSecurityNumber])->get();
    }
}
