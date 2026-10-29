<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('department_id')->nullable();
            $table->string('employee_number');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            
            // Encrypted Sensitive Personal Information (SPI) under RA 10173
            $table->text('tin_encrypted')->nullable();
            $table->text('sss_no_encrypted')->nullable();
            $table->text('gsis_no_encrypted')->nullable();
            $table->text('philhealth_no_encrypted')->nullable();
            $table->text('pagibig_no_encrypted')->nullable();
            $table->text('bank_account_encrypted')->nullable();
            $table->string('bank_name')->nullable();

            $table->boolean('is_faculty')->default(false);
            $table->string('faculty_rank')->nullable(); // e.g., Instructor I, Associate Professor
            $table->enum('status', ['active', 'on_leave', 'probationary', 'resigned', 'retired', 'terminated'])
                  ->default('active');
            $table->date('date_hired');
            $table->date('date_separated')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->unique(['tenant_id', 'employee_number']);
            $table->index(['tenant_id', 'is_faculty', 'status']);
        });

        Schema::create('employment_contracts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->enum('contract_type', ['regular', 'probationary', 'part_time', 'contractual', 'substitute'])
                  ->default('regular');
            $table->enum('salary_basis', ['monthly', 'semi_monthly', 'hourly', 'per_unit'])
                  ->default('monthly');
            $table->decimal('base_rate', 12, 4); // Always decimal(12,4)
            $table->decimal('de_minimis_allowance', 12, 4)->default(0.0000);
            $table->decimal('taxable_allowance', 12, 4)->default(0.0000);
            $table->date('effective_date');
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['tenant_id', 'employee_id', 'is_active']);
        });

        Schema::create('faculty_loads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->uuid('academic_term_id');
            $table->string('subject_code')->nullable();
            $table->string('subject_title')->nullable();
            $table->decimal('lecture_units', 5, 2)->default(0.00);
            $table->decimal('lab_contact_hours', 5, 2)->default(0.00);
            $table->decimal('lab_multiplier', 5, 4)->default(0.7500);
            $table->decimal('credited_load_units', 5, 2)->default(0.00);
            $table->decimal('overload_rate_per_unit', 10, 4)->default(0.0000);
            $table->boolean('is_approved_by_dean')->default(false);
            $table->uuid('approved_by_user_id')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('academic_term_id')->references('id')->on('academic_terms')->onDelete('cascade');
            $table->index(['tenant_id', 'employee_id', 'academic_term_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('faculty_loads');
        Schema::dropIfExists('employment_contracts');
        Schema::dropIfExists('employees');
    }
};

