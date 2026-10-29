<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shifts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('break_minutes')->default(60);
            $table->unsignedSmallInteger('grace_period_minutes')->default(15);
            $table->boolean('is_default')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'code']);
        });

        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->uuid('shift_id')->nullable();
            $table->date('log_date');
            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_out')->nullable();
            $table->enum('status', ['present', 'absent', 'half_day', 'on_leave', 'rest_day', 'holiday'])
                  ->default('present');
            $table->unsignedInteger('tardiness_minutes')->default(0);
            $table->unsignedInteger('undertime_minutes')->default(0);
            $table->unsignedInteger('overtime_minutes')->default(0);
            $table->string('source')->default('manual_dtr'); // e.g. biometric_zkteco, biometric_csv, manual_dtr
            $table->boolean('is_verified')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('shift_id')->references('id')->on('shifts')->onDelete('set null');
            $table->unique(['tenant_id', 'employee_id', 'log_date']);
            $table->index(['tenant_id', 'log_date', 'status']);
        });

        Schema::create('leave_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code'); // e.g., VL, SL, SIL, MATERNITY, PATERNITY, SOLO_PARENT
            $table->enum('category', ['statutory', 'institutional'])->default('statutory');
            $table->boolean('is_paid')->default(true);
            $table->decimal('days_per_year', 5, 2)->default(0.00);
            $table->boolean('is_cumulative')->default(false);
            $table->boolean('is_cash_convertible')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->unique(['tenant_id', 'code']);
        });

        Schema::create('leave_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->uuid('leave_type_id');
            $table->unsignedSmallInteger('year');
            $table->decimal('allocated_days', 5, 2)->default(0.00);
            $table->decimal('used_days', 5, 2)->default(0.00);
            $table->decimal('pending_days', 5, 2)->default(0.00);
            $table->decimal('remaining_days', 5, 2)->default(0.00);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            $table->unique(['tenant_id', 'employee_id', 'leave_type_id', 'year']);
        });

        Schema::create('leave_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->uuid('leave_type_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('days_count', 5, 2);
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved_by_head', 'approved_by_hr', 'rejected', 'cancelled'])
                  ->default('pending');
            $table->uuid('approved_by_head_id')->nullable();
            $table->timestamp('approved_by_head_at')->nullable();
            $table->uuid('approved_by_hr_id')->nullable();
            $table->timestamp('approved_by_hr_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('leave_type_id')->references('id')->on('leave_types')->onDelete('cascade');
            $table->index(['tenant_id', 'employee_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('leave_balances');
        Schema::dropIfExists('leave_types');
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('shifts');
    }
};

