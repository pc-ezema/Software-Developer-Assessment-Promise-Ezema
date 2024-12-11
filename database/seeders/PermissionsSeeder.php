<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view_roles',
            'create_role',
            'update_role',
            'delete_role',
            'view_role_permissions',
            'assign_role_permission',
            'delete_role_permission',
            'view_permissions',
            'add_permission',
            'update_permission',
            'delete_permission',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
