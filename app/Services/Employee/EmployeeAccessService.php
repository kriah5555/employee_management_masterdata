<?php

namespace App\Services\Employee;

use App\Models\User\User;

class EmployeeAccessService
{
    public function getUserPermissions()
    {
        try {
            DB::connection('tenant')->beginTransaction();
                $roles       = config('roles_permissions.ROLES');
                $permissions = config('roles_permissions.PERMISSIONS');
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
