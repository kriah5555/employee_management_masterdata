<?php

namespace App\Repositories\Employee;

use App\Models\User\User;
use App\Models\User\CompanyUser;
use App\Services\User\UserService;
use App\Repositories\User\UserRepository;
use App\Services\Employee\EmployeeService;
use App\Models\Company\Employee\EmployeeProfile;
use App\Interfaces\Employee\ResponsiblePersonInterface;
use App\Repositories\Employee\EmployeeProfileRepository;

class ResponsiblePersonRepository implements ResponsiblePersonInterface
{

    protected $roles;

    public function __construct()
    {
        $this->roles = array_keys(config('roles_permissions.RESPONSIBLE_PERSON_ROLES'));
    }
    public function getCompanyResponsiblePersons($company_id)
    {
        $user_ids = $this->getCompanyResponsiblePersonUserIds($company_id);
        return EmployeeProfile::whereIn('user_id', $user_ids)
            ->with(['user.userBasicDetails'])
            ->get();
    }

    public function getCompanyResponsiblePersonUserIds($company_id)
    {
        $roles = $this->roles;
        return CompanyUser::where('company_id', $company_id)->with("roles")->whereHas("roles", function ($q) use ($roles) {
            $q->whereIn("name", $roles);
        })->get()->pluck('user_id')->toArray();
    }

    public function getCompanyResponsiblePersonOptions($company_id)
    {
        $user_ids = $this->getCompanyResponsiblePersonUserIds($company_id);
        return formatEmployees(EmployeeProfile::whereIn('user_id', $user_ids)
            ->with(['user.userBasicDetails'])
            ->get());
    }

    public function getResponsiblePersonById(string $employee_profile_id, string $company_id)
    {
        $employee_profile = $this->getEmployeeProfileById($employee_profile_id);

        $user_ids     = $this->getCompanyResponsiblePersonUserIds($company_id);
        $user_service = app(UserService::class);
        $user         = User::whereIn('id', $user_ids)
                        ->with(['userBasicDetails', 'userContactDetails'])
                        ->findOrFail($employee_profile->user_id);
        $user->roles = $user_service->getCompanyUserRoles($user->id, $company_id);
        return $user;
    }

    public function getEmployeeProfileById(mixed $employee_profile_id, array $relations = []) 
    {
        return app(EmployeeProfileRepository::class)->getEmployeeProfileById($employee_profile_id, $relations);
    }

    public function deleteResponsiblePerson(string $responsible_person_id, string $company_id)
    {
        $company_user = CompanyUser::where(['company_id' => $company_id, 'user_id' => $responsible_person_id])->get()->first();
        $company_user->roles()->delete();
    }

    public function createResponsiblePerson(array $responsible_person_details, string $company_id)
    {
        $employee_service = app(EmployeeService::class);
        return $employee_service->createNewResponsiblePerson($responsible_person_details, $company_id);
    }

    public function updateResponsiblePerson(string $employee_profile_id, array $responsible_person_details, string $company_id)
    {
        $employee_profile = $this->getEmployeeProfileById($employee_profile_id);

        app(UserRepository::class)->updateUser($employee_profile->user_id, ['social_security_number' => $responsible_person_details['social_security_number']]);
        app(UserService::class)->updateUserDetails($employee_profile->user, $responsible_person_details);

        $company_user = CompanyUser::where(['company_id' => $company_id, 'user_id' => $employee_profile->user_id])->get()->first();
        $roles = [$responsible_person_details['role']];
        if ($company_user->hasRole('employee')) {
            $roles[] = 'employee';
        }
        $company_user->roles()->detach();
        foreach ($roles as $role) {
            $company_user->assignRole($role);
        }
        return $employee_profile;
    }
    public function getResponsiblePersonDetails(int $employeeProfileId, string $company_id)
    {
        $employeeProfile = EmployeeProfile::with('user.userBasicDetails', 'user.userContactDetails')->findOrFail($employeeProfileId);
        $employeeProfile->user->companyUserByCompanyId($company_id);
        return $employeeProfile;
    }
}
