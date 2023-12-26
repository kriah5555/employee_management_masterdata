<?php

namespace App\Repositories\Employee;

use App\Models\User\User;
use App\Models\User\CompanyUser;
use App\Services\User\UserService;
use App\Repositories\User\UserRepository;
use App\Services\Employee\EmployeeService;
use App\Models\Company\Employee\EmployeeProfile;
use App\Interfaces\Employee\ResponsiblePersonInterface;

class ResponsiblePersonRepository implements ResponsiblePersonInterface
{

    protected $roles;

    public function __construct()
    {
        $this->roles = array_keys(config('roles_permissions.RESPONSIBLE_PERSON_ROLES'));
    }
    public function getCompanyResponsiblePersons($companyId)
    {
        $roles = array_keys(config('roles_permissions.RESPONSIBLE_PERSON_ROLES'));
        return CompanyUser::where('company_id', $companyId)->with(['roles', 'user', 'user.employeeProfileForCompany'])->whereHas("roles", function ($q) use ($roles) {
            $q->whereIn("name", $roles);
        })->get();

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

    public function getResponsiblePersonById(string $user_id, string $company_id)
    {
        $user_ids = $this->getCompanyResponsiblePersonUserIds($company_id);
        $user_service = app(UserService::class);
        $user = User::whereIn('id', $user_ids)
            ->with(['userBasicDetails', 'roles', 'userContactDetails'])
            ->findOrFail($user_id);

        unset($user->roles);
        $user->roles = $user_service->getCompanyUserRoles($user->id, $company_id);
        return $user;
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

    public function updateResponsiblePerson(string $responsible_person_id, array $responsible_person_details, string $company_id)
    {
        $responsiblePerson = EmployeeProfile::findOrFail($responsible_person_id);
        app(UserRepository::class)->updateUser($responsiblePerson->user->id, ['social_security_number' => $responsible_person_details['social_security_number']]);
        app(UserService::class)->updateUserDetails($responsiblePerson->user, $responsible_person_details);

        $company_user = CompanyUser::where(['company_id' => $company_id, 'user_id' => $responsiblePerson->user->id])->get()->first();
        $roles = [$responsible_person_details['role']];
        if ($company_user->hasRole('employee')) {
            $roles[] = 'employee';
        }
        $company_user->roles()->detach();
        foreach ($roles as $role) {
            $company_user->assignRole($role);
        }
        return $responsiblePerson;
    }
    public function getResponsiblePersonDetails(int $employeeProfileId, string $company_id)
    {
        $employeeProfile = EmployeeProfile::with('user.userBasicDetails', 'user.userContactDetails')->findOrFail($employeeProfileId);
        $employeeProfile->user->companyUserByCompanyId($company_id);
        return $employeeProfile;
    }
}
