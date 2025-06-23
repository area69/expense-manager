<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Permission list
        $permissions = [
            'manage-users',
            'manage-roles',
            'manage-categories',
            'manage-all-expenses',
            'manage-own-expenses',
        ];

        // Create permissions if not exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission, 'guard_name' => 'web']
            );
        }

        // Create roles if not exist
        $adminRole = Role::firstOrCreate(['name' => 'Administrator', 'guard_name' => 'web']);
        $userRole  = Role::firstOrCreate(['name' => 'User', 'guard_name' => 'web']);

        // Sync permissions with roles (overwrite old ones safely)
        $adminRole->syncPermissions($permissions);
        $userRole->syncPermissions(['manage-own-expenses']);
    }
}
