<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class HrisModulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_admin_can_access_hris_modules(): void
    {
        \Illuminate\Support\Facades\Artisan::call('db:seed');

        $superAdmin = User::where('role', 'super-admin')->first();

        // 1. People module data
        $peopleRes = $this->actingAs($superAdmin)->getJson('/api/v1/modules/people');
        $peopleRes->assertStatus(200);
        $peopleRes->assertJsonStructure(['employees', 'plantilla', 'positions', 'assignments', 'movements']);

        // 2. Time & Leave module data
        $timeLeaveRes = $this->actingAs($superAdmin)->getJson('/api/v1/modules/time-leave');
        $timeLeaveRes->assertStatus(200);
        $timeLeaveRes->assertJsonStructure(['dtr_logs', 'leave_requests', 'leave_balances', 'holidays', 'overtime']);

        // 3. Payroll Suite module data
        $payrollRes = $this->actingAs($superAdmin)->getJson('/api/v1/modules/payroll-suite');
        $payrollRes->assertStatus(200);
        $payrollRes->assertJsonStructure(['payroll_runs', 'remittances', 'loans', 'bank_accounts']);

        // 4. Recruitment & Performance Suite module data
        $recruitmentRes = $this->actingAs($superAdmin)->getJson('/api/v1/modules/recruitment');
        $recruitmentRes->assertStatus(200);
        $recruitmentRes->assertJsonStructure([
            'vacancies',
            'requisitions',
            'job_postings',
            'applicants',
            'interviews',
            'assessments',
            'job_offers',
            'pre_employment_requirements',
            'onboarding',
            'performance_cycles',
            'final_ratings',
            'templates',
            'placeholders',
            'service_requests',
            'system_cache',
        ]);
    }
}

