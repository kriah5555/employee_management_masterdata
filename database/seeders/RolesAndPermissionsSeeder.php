<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $permissions = [
            [
                'name'       => 'Web app access',
                'guard_name' => 'api'
            ],
            [
                'name'       => 'Mobile app access',
                'guard_name' => 'api'
            ],
            [
                'name'       => 'Access all companies',
                'guard_name' => 'api'
            ],
        ];
        foreach ($permissions as $permissionDetails) {
            Permission::create($permissionDetails);
        }

        $roles = [
            [
                'guard_name'  => 'api',
                'name'        => 'superadmin',
                'permissions' => [
                    'Web app access',
                    'Mobile app access',
                    'Access all companies'
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'admin',
                'permissions' => [
                    'Web app access',
                    'Mobile app access',
                    'Access all companies'
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'moderator'
                ,
                'permissions' => [
                    'Web app access',
                    'Mobile app access',
                    'permissions' => [
                        'Web app access',
                        'Mobile app access',
                        'Access all companies'
                    ]
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'customer_admin'
                ,
                'permissions' => [
                    'Web app access',
                    'Mobile app access'
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'hr_manager'
                ,
                'permissions' => [
                    'Web app access',
                    'Mobile app access'
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'manager'
                ,
                'permissions' => [
                    'Web app access',
                    'Mobile app access'
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'planner'
                ,
                'permissions' => [
                    'Web app access',
                    'Mobile app access'
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'staff'
                ,
                'permissions' => [
                    'Web app access',
                    'Mobile app access'
                ]
            ],
            [
                'guard_name'  => 'api',
                'name'        => 'employee'
                ,
                'permissions' => [
                    'Mobile app access'
                ]
            ],
        ];
        foreach ($roles as $roleDetails) {
            $rolePermissions = $roleDetails['permissions'];
            unset($roleDetails['permissions']);
            $role = Role::create($roleDetails);
            $role->givePermissionTo($rolePermissions);
        }
    }
}
