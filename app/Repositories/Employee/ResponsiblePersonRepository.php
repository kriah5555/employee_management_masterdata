<?php

namespace App\Repositories\Employee;

use App\Interfaces\Employee\ResponsiblePersonInterface;
use App\Models\User\User;
use App\Models\User\CompanyUser;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Services\Employee\EmployeeService;
use App\Services\User\UserService;

class ResponsiblePersonRepository implements ResponsiblePersonInterface
{

    protected $roles;

    public function __construct()
    {
        $this->roles = [config('roles_permissions.MANAGER'), config('roles_permissions.CUSTOMER_ADMIN')];
    }

    public function getCompanyResponsiblePersonUserIds($company_id)
    {
        $roles = $this->roles;

        return CompanyUser::where('company_id', $company_id)
        ->get()
        ->filter(function ($user) use ($roles, $company_id) {
            $user_roles = app(UserService::class)->getCompanyUserRoles($user->user_id, $company_id);
            return !empty(array_intersect($roles, $user_roles));
        })
        ->pluck('user_id')
        ->toArray();

        // return CompanyUser::with(['roles' => function ($query) use ($roles) {
        //     $query->whereIn('name', $roles);
        // }])
        // ->where('company_id', $company_id)->get()->pluck('user_id');
    }

    public function getCompanyResponsiblePersons($company_id)
    {
        $user_ids = $this->getCompanyResponsiblePersonUserIds($company_id);
        return User::whereIn('id', $user_ids)
        ->with(['userBasicDetails'])
        ->get();
    }

    public function getResponsiblePersonById(string $user_id, string $company_id, $formatted_roles = true) : User
    {
        $user_ids     = $this->getCompanyResponsiblePersonUserIds($company_id);
        $user_service = app(UserService::class);
        $user         = User::whereIn('id', $user_ids)
                        ->with(['userBasicDetails', 'roles'])
                        ->findOrFail($user_id);

        unset($user->roles);
        $user->roles = $user_service->getCompanyUserRoles($user->id, $company_id);
        return $user;
    }

    public function deleteResponsiblePerson(string $responsible_person_id, string $company_id)
    {
        $company_user = CompanyUser::where(['company_id' => $company_id, 'user_id' => $responsible_person_id])->get()->first();
        $company_user->removeRole(config('roles_permissions.CUSTOMER_ADMIN')); # detach the role
        $company_user->removeRole(config('roles_permissions.MANAGER')); # detach the role
    }

    public function createResponsiblePerson(array $responsible_person_details, string $company_id)
    {
        $employee_service = app(EmployeeService::class);
        return $employee_service->createNewResponsiblePerson($responsible_person_details, $company_id);
    }

    public function updateResponsiblePerson(string $responsible_person_id, array $responsible_person_details, string $company_id)
    {
        $responsible_person                         = $this->getResponsiblePersonById($responsible_person_id, $company_id, false);
        $responsible_person->social_security_number = $responsible_person_details['social_security_number'];
        $userBasicDetails                           = $responsible_person->userBasicDetails;

        if ($userBasicDetails) {
            $userBasicDetails->update($responsible_person_details);
        }

        $company_user = CompanyUser::where(['company_id' => $company_id, 'user_id' => $responsible_person_id])->get()->first();
        
        if (!$company_user->hasRole($responsible_person_details['role'])) { # check if the roles are same else update the role
            $company_user->removeRole(config('roles_permissions.CUSTOMER_ADMIN')); # detach the role
            $company_user->removeRole(config('roles_permissions.MANAGER')); # detach the role
            $company_user->assignRole($responsible_person_details['role']);
        }
        
        return $responsible_person;
    }
}
