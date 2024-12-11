<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\RolePermissionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::get('/login', function () {
    return response([
        'success' => false,
        'message' => 'Token Required!'
    ], 401);
})->name('login');

// Version 1
Route::prefix('v1')->middleware(['cors', 'json.response'])->group(function () {
    // Auth routes
    Route::get('/view/roles', [AuthController::class, 'getRoles']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/forget-password', [AuthController::class, 'forget_password']);
    Route::post('/reset-password', [AuthController::class, 'reset_password']);

    Route::middleware('auth:api')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);

        // Role and Permission routes
        Route::middleware(['permission:view_roles'])->get('/roles', [RolePermissionController::class, 'getRoles']);
        Route::middleware(['permission:create_role'])->post('/role/create', [RolePermissionController::class, 'createRole']);
        Route::middleware(['permission:update_role'])->put('/role/update', [RolePermissionController::class, 'updateRole']);
        Route::middleware(['permission:delete_role'])->delete('/role/delete', [RolePermissionController::class, 'deleteRole']);

        Route::middleware(['permission:view_role_permissions'])->get('/roles/view/permissions', [RolePermissionController::class, 'viewRolePermissions']);
        Route::middleware(['permission:assign_role_permission'])->post('/roles/assign/permissions', [RolePermissionController::class, 'assignRolePermission']);
        Route::middleware(['permission:delete_role_permission'])->delete('/roles/delete/permissions', [RolePermissionController::class, 'deleteRolePermission']);

        Route::middleware(['permission:view_permissions'])->get('/permissions', [RolePermissionController::class, 'getPermissions']);
        Route::middleware(['permission:add_permission'])->post('/permission/add', [RolePermissionController::class, 'addPermission']);
        Route::middleware(['permission:update_permission'])->put('/permission/update', [RolePermissionController::class, 'updatePermission']);
        Route::middleware(['permission:delete_permission'])->delete('/permission/delete', [RolePermissionController::class, 'deletePermission']);
    });
});
