<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In - HERA v3');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::create([
            'name'      => 'Super Administrator',
            'email'     => 'admin@hera.ph',
            'password'  => Hash::make('Password123!'),
            'role'      => 'super-admin',
            'is_active' => true,
        ]);

        $response = $this->post('/login', [
            'email'    => 'admin@hera.ph',
            'password' => 'Password123!',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');
    }

    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::create([
            'name'      => 'Super Administrator',
            'email'     => 'admin@hera.ph',
            'password'  => Hash::make('Password123!'),
            'role'      => 'super-admin',
            'is_active' => true,
        ]);

        $this->post('/login', [
            'email'    => 'admin@hera.ph',
            'password' => 'WrongPassword',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::create([
            'name'      => 'Super Administrator',
            'email'     => 'admin@hera.ph',
            'password'  => Hash::make('Password123!'),
            'role'      => 'super-admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::create([
            'name'      => 'Super Administrator',
            'email'     => 'admin@hera.ph',
            'password'  => Hash::make('Password123!'),
            'role'      => 'super-admin',
            'is_active' => true,
        ]);

        $response = $this->actingAs($user)->get('/');
        $response->assertStatus(200);
        $response->assertSee('admin-settings-mount');
    }

    public function test_superadmin_can_manage_users_and_roles(): void
    {
        \Illuminate\Support\Facades\Artisan::call('db:seed');

        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@hera.ph'],
            [
                'name'      => 'Super Administrator',
                'password'  => Hash::make('Password123!'),
                'role'      => 'super-admin',
                'is_active' => true,
            ]
        );

        // 1. Fetch users & roles list
        $res = $this->actingAs($superAdmin)->getJson('/api/v1/admin/users');
        $res->assertStatus(200);
        $res->assertJsonStructure(['users', 'total_count', 'roles', 'tenants']);

        // 2. Create new user
        $createRes = $this->actingAs($superAdmin)->postJson('/api/v1/admin/users', [
            'name'     => 'Teacher Clara',
            'email'    => 'clara@example.com',
            'role'     => 'employee',
            'password' => 'Password123!',
        ]);
        $createRes->assertStatus(201);
        $newUserId = $createRes->json('user.id');

        // 3. Toggle user access
        $toggleRes = $this->actingAs($superAdmin)->patchJson("/api/v1/admin/users/{$newUserId}/toggle-access");
        $toggleRes->assertStatus(200);
        $this->assertFalse($toggleRes->json('is_active'));

        // 4. Update role permissions
        $permRes = $this->actingAs($superAdmin)->putJson('/api/v1/admin/roles/hr-officer/permissions', [
            'permissions' => [
                'users.view' => true,
                'leave.approve' => true,
            ],
        ]);
        $permRes->assertStatus(200);
    }

    public function test_hr_officer_has_restricted_permissions_and_cannot_modify_roles(): void
    {
        $hrUser = User::create([
            'name'      => 'HR Maria',
            'email'     => 'hr.maria@example.com',
            'password'  => Hash::make('Password123!'),
            'role'      => 'hr-officer',
            'is_active' => true,
        ]);

        // 1. HR visiting dashboard receives their role context
        $viewRes = $this->actingAs($hrUser)->get('/');
        $viewRes->assertStatus(200);
        $viewRes->assertSee('Human Resources Officer');

        // 2. HR cannot modify role permissions (403 Forbidden)
        $permRes = $this->actingAs($hrUser)->putJson('/api/v1/admin/roles/super-admin/permissions', [
            'permissions' => ['*'],
        ]);
        $permRes->assertStatus(403);

        // 3. HR cannot create accounts (403 Forbidden)
        $createRes = $this->actingAs($hrUser)->postJson('/api/v1/admin/users', [
            'name'     => 'Unauthorized User',
            'email'    => 'unauth@example.com',
            'role'     => 'super-admin',
            'password' => 'Password123!',
        ]);
        $createRes->assertStatus(403);

        // 4. HR cannot toggle access (403 Forbidden)
        $toggleRes = $this->actingAs($hrUser)->patchJson("/api/v1/admin/users/{$hrUser->id}/toggle-access");
        $toggleRes->assertStatus(403);
    }
}

