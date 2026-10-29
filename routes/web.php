<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        $user = auth()->user();
        $roleNames = [
            'super-admin'      => 'Super Administrator',
            'tenant-admin'     => 'Institution Administrator',
            'hr-officer'       => 'Human Resources Officer',
            'academic-head'    => 'Academic Dean / Dept Head',
            'payroll-preparer' => 'Payroll Officer (Preparer)',
            'payroll-approver' => 'Finance Comptroller (Approver)',
            'auditor'          => 'Internal / External Auditor',
            'employee'         => 'Faculty & Staff (ESS)',
        ];

        $userData = [
            'id'                      => $user->id,
            'name'                    => $user->name,
            'email'                   => $user->email,
            'role'                    => $user->role,
            'role_name'               => $user->roleModel?->name ?? ($roleNames[$user->role] ?? ucfirst(str_replace('-', ' ', $user->role))),
            'tenant_id'               => $user->tenant_id,
            'tenant_name'             => $user->tenant?->name ?? 'Global Platform',
            'avatar'                  => $user->avatar,
            'is_super_admin'          => $user->isSuperAdmin(),
            'can_manage_roles'        => $user->isSuperAdmin(),
            'can_manage_users'        => $user->hasRole('super-admin', 'tenant-admin'),
            'can_manage_institutions' => $user->isSuperAdmin(),
            'can_manage_personnel'    => $user->hasRole('super-admin', 'tenant-admin', 'hr-officer'),
            'can_approve_payroll'     => $user->hasRole('super-admin', 'payroll-approver'),
            'can_prepare_payroll'     => $user->hasRole('super-admin', 'payroll-preparer'),
            'permissions'             => $user->isSuperAdmin()
                ? ['*']
                : array_keys(array_filter($user->roleModel?->permissions ?? [])),
        ];

        return view('welcome', ['currentUser' => $userData]);
    });

    // Admin & User Management APIs
    Route::prefix('api/v1/admin')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index']);
        Route::post('/users', [UserManagementController::class, 'store']);
        Route::patch('/users/{id}/toggle-access', [UserManagementController::class, 'toggleAccess']);
        Route::delete('/users/{id}', [UserManagementController::class, 'destroy']);
        Route::put('/roles/{roleId}/permissions', [UserManagementController::class, 'updateRolePermissions']);
    });
});
