<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assign permissions to 'admin' role
        $adminRole = Role::where('name', 'admin')->first();
        $permissions = Permission::all();

        $adminRole->permissions()->sync($permissions->pluck('id')); // All permissions for admin

        // Assign limited permissions to 'user' role
        $userRole = Role::where('name', 'user')->first();
        $userPermissions = Permission::whereIn('name', ['view_role', 'view_role_permissions'])->get();

        $userRole->permissions()->sync($userPermissions->pluck('id'));
    }
}
