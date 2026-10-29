<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /**
     * Get paginated list of users with search and role filters.
     */
    public function index(Request $request): JsonResponse
    {
        $currentUser = $request->user();

        // Standard employees cannot access administrative user directory
        if ($currentUser->role === 'employee') {
            return response()->json([
                'message' => 'Unauthorized: Employees cannot access administrative directory.',
            ], 403);
        }

        $query = User::with('tenant');

        // Scoping: If not super-admin, restrict user directory strictly to their own tenant
        if (! $currentUser->isSuperAdmin()) {
            $query->where('tenant_id', $currentUser->tenant_id);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('role', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        $users = $query->orderBy('created_at', 'desc')->get()->map(function ($u) {
            return [
                'id'            => $u->id,
                'name'          => $u->name,
                'email'         => $u->email,
                'role'          => $u->role,
                'tenant_name'   => $u->tenant ? $u->tenant->name : 'Global Platform',
                'tenant_id'     => $u->tenant_id,
                'is_active'     => (bool) $u->is_active,
                'status'        => $u->is_active ? 'Active' : 'Inactive',
                'created_at'    => $u->created_at ? \Carbon\Carbon::parse($u->created_at)->format('M d, Y') : null,
                'last_login_at' => $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->format('M d, Y') : 'Never',
                'avatar'        => $u->avatar,
            ];
        });

        // Only super-admins can see and manage platform role permission sets
        $roles = $currentUser->isSuperAdmin() ? Role::all() : [];
        $tenants = $currentUser->isSuperAdmin()
            ? Tenant::where('is_active', true)->get(['id', 'name', 'code'])
            : Tenant::where('id', $currentUser->tenant_id)->get(['id', 'name', 'code']);

        return response()->json([
            'users'            => $users,
            'total_count'      => $users->count(),
            'roles'            => $roles,
            'tenants'          => $tenants,
            'can_manage_roles' => $currentUser->isSuperAdmin(),
            'can_manage_users' => $currentUser->hasRole('super-admin', 'tenant-admin'),
        ]);
    }

    /**
     * Create a new user account.
     */
    public function store(Request $request): JsonResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('super-admin', 'tenant-admin')) {
            return response()->json([
                'message' => 'Unauthorized: Only platform super-admins and institution administrators can create user accounts.',
            ], 403);
        }

        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'role'      => ['required', 'string', 'exists:roles,id'],
            'tenant_id' => ['nullable', 'exists:tenants,id'],
            'password'  => ['required', 'string', 'min:8'],
        ]);

        // Prevent non-superadmins from assigning super-admin role
        if ($validated['role'] === 'super-admin' && ! $currentUser->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized: Only Super Administrators can assign the Super Administrator role.',
            ], 403);
        }

        // Institution admins can only create accounts within their own institution
        $assignedTenantId = $currentUser->isSuperAdmin()
            ? ($validated['role'] === 'super-admin' ? null : ($validated['tenant_id'] ?? null))
            : $currentUser->tenant_id;

        $user = User::create([
            'name'          => $validated['name'],
            'email'         => $validated['email'],
            'role'          => $validated['role'],
            'tenant_id'     => $assignedTenantId,
            'password'      => Hash::make($validated['password']),
            'is_active'     => true,
            'last_login_at' => now(),
        ]);

        return response()->json([
            'message' => 'User account created successfully.',
            'user'    => $user,
        ], 201);
    }

    /**
     * Toggle active/inactive access for a user.
     */
    public function toggleAccess(Request $request, int $id): JsonResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('super-admin', 'tenant-admin')) {
            return response()->json([
                'message' => 'Unauthorized: Insufficient privileges to toggle user access.',
            ], 403);
        }

        $user = User::findOrFail($id);

        // Prevent disabling primary super-admin
        if ($user->email === 'admin@hera.ph' && $user->is_active) {
            return response()->json([
                'message' => 'Primary platform superadmin cannot be deactivated.',
            ], 422);
        }

        // Institution admins cannot modify users outside their tenant, nor modify superadmins
        if (! $currentUser->isSuperAdmin()) {
            if ($user->tenant_id !== $currentUser->tenant_id || $user->isSuperAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized: You can only manage users within your own institution.',
                ], 403);
            }
        }

        $user->is_active = ! $user->is_active;
        $user->save();

        return response()->json([
            'message'   => 'User access status updated.',
            'is_active' => $user->is_active,
            'status'    => $user->is_active ? 'Active' : 'Inactive',
        ]);
    }

    /**
     * Update permissions for a specific role.
     */
    public function updateRolePermissions(Request $request, string $roleId): JsonResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->isSuperAdmin()) {
            return response()->json([
                'message' => 'Unauthorized: Only Super Administrators can modify platform role permissions.',
            ], 403);
        }

        $role = Role::findOrFail($roleId);

        $validated = $request->validate([
            'permissions' => ['required', 'array'],
        ]);

        $role->permissions = $validated['permissions'];
        $role->save();

        return response()->json([
            'message' => "Permissions for role '{$role->name}' updated successfully.",
            'role'    => $role,
        ]);
    }

    /**
     * Delete user account.
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $currentUser = $request->user();

        if (! $currentUser->hasRole('super-admin', 'tenant-admin')) {
            return response()->json([
                'message' => 'Unauthorized: Insufficient privileges to delete user accounts.',
            ], 403);
        }

        $user = User::findOrFail($id);

        if ($user->email === 'admin@hera.ph') {
            return response()->json([
                'message' => 'Primary superadmin account cannot be deleted.',
            ], 422);
        }

        // Institution admins cannot delete users outside their tenant, nor delete superadmins
        if (! $currentUser->isSuperAdmin()) {
            if ($user->tenant_id !== $currentUser->tenant_id || $user->isSuperAdmin()) {
                return response()->json([
                    'message' => 'Unauthorized: You can only delete users within your own institution.',
                ], 403);
            }
        }

        $user->delete();

        return response()->json([
            'message' => 'User account deleted successfully.',
        ]);
    }
}
