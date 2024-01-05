<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\EmployeeProfileRepositoryInterface;
use App\Models\Company\Employee\EmployeeProfile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Models\User\User;
use App\Models\EmployeeType\EmployeeType;

class EmployeeProfileRepository implements EmployeeProfileRepositoryInterface
{
    public function getAllEmployeeProfiles(array $relations = [])
    {
        return EmployeeProfile::with($relations)->get();
    }

    public function getEmployeeProfileById(mixed $id, array $relations = []): Collection|Builder|EmployeeProfile
    {
        return EmployeeProfile::with($relations)->findOrFail($id);
    }

    public function getEmployeeProfileByUserId($user_id)
    {
        return EmployeeProfile::where(['user_id' => $user_id])->get()->first();
    }

    public function deleteEmployeeProfile(string $employeeProfileId)
    {
        EmployeeProfile::destroy($employeeProfileId);
    }

    public function createEmployeeProfile(array $employeeProfileDetails): EmployeeProfile
    {
        return EmployeeProfile::updateOrCreate(
            [
                'user_id' => $employeeProfileDetails['user_id'],
            ],
            []
        );
        // create($employeeProfileDetails);
    }

    public function updateEmployeeProfile(string $employeeProfileId, array $newDetails)
    {
        return EmployeeProfile::whereId($employeeProfileId)->update($newDetails);
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

    public function getEmployeeOptions()
    {
        try {
            $employees = $this->getAllEmployeeProfiles([
                'user',
                'user.userBasicDetails',
                'user.userContactDetails',
                'user.userProfilePicture'
            ]);

            return formatEmployees($employees);

        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeesForHoliday()
    {

        $employee_types_with_holiday_access = EmployeeType::with([
            'employeeTypeConfig' => function ($employeeTypeConfig) {
                $employeeTypeConfig->where('holiday_access', true);
            }
        ])->get();

        $employee_type_ids_with_holiday_access = $employee_types_with_holiday_access->pluck('id');

        $employees = EmployeeProfile::with([
            'user',
            'user.userBasicDetails',
            'employeeContracts' => function ($employeeContracts) use ($employee_type_ids_with_holiday_access) {
                $employeeContracts->whereIn('employee_type_id', $employee_type_ids_with_holiday_access);
            }
        ])->get();

        return formatEmployees($employees);
    }

    public function getEmployeesForLeave()
    {
        $employee_types_with_holiday_access = EmployeeType::with([
            'employeeTypeConfig' => function ($employeeTypeConfig) {
                $employeeTypeConfig->where('leave_access', true);
            }
        ])->get();

        $employee_type_ids_with_holiday_access = $employee_types_with_holiday_access->pluck('id');

        $employees = EmployeeProfile::with([
            'user',
            'user.userBasicDetails',
            'employeeContracts' => function ($employeeContracts) use ($employee_type_ids_with_holiday_access) {
                $employeeContracts->whereIn('employee_type_id', $employee_type_ids_with_holiday_access);
            }
        ])->get();

        return formatEmployees($employees);
    }
}
