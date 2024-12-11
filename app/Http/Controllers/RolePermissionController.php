<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RolePermissionController extends Controller
{
    public function getRoles()
    {
        $roles = Role::all();
        return response()->json(['success' => true, 'data' => $roles], 200);
    }

    public function createRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'error' => $validator->errors()
            ], 422);
        }

        $role = Role::create(['name' => strtolower($request->name)]);
        return response()->json(['success' => true, 'data' => $role], 201);
    }

    public function updateRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roleId' => 'required|integer|exists:roles,id',
            'name' => 'required|string|unique:roles,name,' . $request->roleId,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'error' => $validator->errors()
            ], 422);
        }

        $role = Role::find($request->roleId);

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        $role->update(['name' => strtolower($request->name)]);
        return response()->json(['success' => true, 'data' => $role], 200);
    }

    public function deleteRole(Request $request)
    {
        $role = Role::find($request->roleId);

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        // Check if any users are assigned to this role
        if ($role->users()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Role cannot be deleted as it is assigned to one or more users.',
            ], 400);
        }

        // Detach all associated permissions
        $role->permissions()->detach();

        // Delete the role
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully and all associated permissions detached',
        ], 200);
    }

    public function viewRolePermissions(Request $request)
    {
        $role = Role::find($request->roleId);

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        $permissions = $role->permissions;
        return response()->json(['success' => true, 'data' => $permissions], 200);
    }

    public function assignRolePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roleId' => 'required|integer|exists:roles,id',
            'permissionId' => 'required|integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'error' => $validator->errors()
            ], 422);
        }

        $role = Role::find($request->roleId);

        if (!$role) {
            return response()->json(['success' => false, 'message' => 'Role not found'], 404);
        }

        // Assign the single permission to the role
        if (!$role->permissions->contains($request->permissionId)) {
            $role->permissions()->attach($request->permissionId);
        }

        return response()->json(['success' => true, 'messsage' => 'Permission assigned successfully.'], 200);
    }

    public function deleteRolePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'roleId' => 'required|integer|exists:roles,id',
            'permissionId' => 'required|integer|exists:permissions,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'error' => $validator->errors()
            ], 422);
        }

        $role = Role::find($request->roleId);
        $permission = Permission::find($request->permissionId);

        $role->permissions()->detach($permission->id);
        return response()->json(['success' => true, 'message' => 'Permission removed from role'], 200);
    }

    public function getPermissions()
    {
        $permissions = Permission::all();
        return response()->json(['success' => true, 'data' => $permissions], 200);
    }

    public function addPermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'error' => $validator->errors()
            ], 422);
        }

        $permission = Permission::create(['name' => strtolower($request->name)]);
        return response()->json(['success' => true, 'data' => $permission], 201);
    }

    public function updatePermission(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'permissionId' => 'required|integer|exists:permissions,id',
            'name' => 'required|string|unique:permissions,name,' . $request->permissionId,
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the errors.',
                'error' => $validator->errors()
            ], 422);
        }

        $permission = Permission::find($request->permissionId);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
        }

        $permission->update(['name' => strtolower($request->name)]);
        return response()->json(['success' => true, 'data' => $permission], 200);
    }

    public function deletePermission(Request $request)
    {
        $permission = Permission::find($request->permissionId);

        if (!$permission) {
            return response()->json(['success' => false, 'message' => 'Permission not found'], 404);
        }

        // Detach the permission from all roles before deleting it
        $permission->roles()->detach();

        $permission->delete();
        return response()->json(['success' => true, 'message' => 'Permission deleted successfully and detached from all roles'], 200);
    }
}
