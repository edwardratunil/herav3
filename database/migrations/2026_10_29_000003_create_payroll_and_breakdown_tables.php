<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_runs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('run_number')->nullable();
            $table->date('period_start');
            $table->date('period_end');
            $table->date('payout_date');
            $table->enum('payout_frequency', ['semi_monthly', 'monthly'])->default('semi_monthly');
            $table->enum('status', ['draft', 'calculating', 'pending_approval', 'approved', 'locked', 'cancelled'])
                  ->default('draft');
            
            // Separation of Duties
            $table->uuid('prepared_by_user_id')->nullable();
            $table->timestamp('prepared_at')->nullable();
            $table->uuid('approved_by_user_id')->nullable();
            $table->timestamp('approved_at')->nullable();

            // Cryptographic immutability hash
            $table->char('checksum_hash', 64)->nullable();

            // Total summary snapshot (decimal 14, 4)
            $table->decimal('total_gross_pay', 14, 4)->default(0.0000);
            $table->decimal('total_deductions', 14, 4)->default(0.0000);
            $table->decimal('total_net_pay', 14, 4)->default(0.0000);
            $table->decimal('total_employer_contributions', 14, 4)->default(0.0000);
            $table->unsignedInteger('employee_count')->default(0);

            $table->json('statutory_versions_used')->nullable();
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'status', 'payout_date']);
        });

        Schema::create('payroll_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payroll_run_id');
            $table->uuid('tenant_id');
            $table->uuid('employee_id');

            $table->decimal('basic_earned', 12, 4)->default(0.0000);
            $table->decimal('faculty_overload_pay', 12, 4)->default(0.0000);
            $table->decimal('allowances_taxable', 12, 4)->default(0.0000);
            $table->decimal('allowances_non_taxable', 12, 4)->default(0.0000);
            $table->decimal('gross_pay', 12, 4)->default(0.0000);

            // Statutory EE shares
            $table->decimal('sss_ee', 12, 4)->default(0.0000);
            $table->decimal('gsis_ee', 12, 4)->default(0.0000);
            $table->decimal('philhealth_ee', 12, 4)->default(0.0000);
            $table->decimal('pagibig_ee', 12, 4)->default(0.0000);
            $table->decimal('withholding_tax', 12, 4)->default(0.0000);

            // Statutory ER shares
            $table->decimal('sss_er', 12, 4)->default(0.0000);
            $table->decimal('gsis_er', 12, 4)->default(0.0000);
            $table->decimal('philhealth_er', 12, 4)->default(0.0000);
            $table->decimal('pagibig_er', 12, 4)->default(0.0000);
            $table->decimal('ec_er', 12, 4)->default(0.0000);

            // Deductions & net
            $table->decimal('tardiness_undertime_deduction', 12, 4)->default(0.0000);
            $table->decimal('loan_deductions', 12, 4)->default(0.0000);
            $table->decimal('other_deductions', 12, 4)->default(0.0000);
            $table->decimal('total_deductions', 12, 4)->default(0.0000);
            $table->decimal('net_pay', 12, 4)->default(0.0000);

            $table->json('calculation_snapshot')->nullable();
            $table->timestamps();

            $table->foreign('payroll_run_id')->references('id')->on('payroll_runs')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['payroll_run_id', 'employee_id']);
            $table->index(['tenant_id', 'employee_id']);
        });

        Schema::create('payroll_item_breakdowns', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('payroll_item_id');
            $table->uuid('tenant_id');
            $table->enum('category', ['earning', 'statutory_ee', 'statutory_er', 'loan', 'tax', 'adjustment']);
            $table->string('item_code'); // e.g. SSS_EE, SSS_WISP_EE, PHILHEALTH_EE, BIR_WTAX
            $table->string('item_description');
            $table->decimal('amount', 12, 4);
            $table->string('rate_table_version')->nullable();
            $table->timestamps();

            $table->foreign('payroll_item_id')->references('id')->on('payroll_items')->onDelete('cascade');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['payroll_item_id', 'item_code']);
        });

        Schema::create('payroll_adjustments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('employee_id');
            $table->uuid('origin_payroll_run_id')->nullable();
            $table->uuid('applied_payroll_run_id')->nullable();
            $table->string('reason');
            $table->enum('type', ['addition', 'deduction']);
            $table->decimal('amount', 12, 4);
            $table->boolean('is_taxable')->default(true);
            $table->boolean('is_applied')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->index(['tenant_id', 'employee_id', 'is_applied']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_adjustments');
        Schema::dropIfExists('payroll_item_breakdowns');
        Schema::dropIfExists('payroll_items');
        Schema::dropIfExists('payroll_runs');
    }
};

