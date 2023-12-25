<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\DB;
use App\Models\User\CompanyUser;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Repositories\Employee\EmployeeProfileRepository;

class EmployeeRolesAndPermissionService
{

    public function __construct(
        protected EmployeeProfileRepository $employeeProfileRepository,
    ) {
    }

    public function getEmployeeRolesPermissions($employee_profile_id, $company_id)
    {
        try {
            $employee_profile = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);
            $company_user     = CompanyUser::where(['company_id' => $company_id, 'user_id' => $employee_profile->user_id])->first();

            $roles       = $this->getRoles();
            $permissions = $this->getPermissions();

            $employee_roles = $roles->map(function ($role) use ($company_user) {
                return [
                    'role'   => $role,
                    'status' => $company_user->hasRole($role),
                ];
            });

            $employee_permissions = $permissions->map(function ($permission) use ($company_user) {
                return [
                    'permission' => $permission,
                    'status'     => $company_user->hasPermissionTo($permission),
                ];
            });

            return ['roles' => $employee_roles, 'permissions' => $employee_permissions];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    protected function getRoles()
    {
        return Role::on('master')->get()->pluck('name');
    }

    protected function getPermissions()
    {
        return Permission::on('master')->get()->pluck('name');
    }

    public function updateEmployeeRolesAndPermissions($employee_profile_id, $values, $company_id)
    {
        $employee_profile = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);
        $company_user     = CompanyUser::where(['company_id' => $company_id, 'user_id' => $employee_profile->user_id])->first();

        foreach ($values['roles'] as $updatedRole) {
            $role = $updatedRole['role'];
            $status = $updatedRole['status'];
            if ($status) {
                $company_user->assignRole($role);
            } else {
                $company_user->removeRole($role);
            }
        }

        foreach ($values['permissions'] as $updatedPermission) {
            $permission = $updatedPermission['permission'];
            $status = $updatedPermission['status'];

            if ($status) {
                $company_user->givePermissionTo($permission);
            } else {
                $company_user->revokePermissionTo($permission);
            }
        }
    }
}
