<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tenants', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('institution_type', ['private_hei', 'private_k12', 'deped', 'suc', 'lgu'])
                  ->default('private_hei');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('campuses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('name');
            $table->string('code')->nullable();
            $table->string('address')->nullable();
            $table->boolean('is_main_campus')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'code']);
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->uuid('campus_id')->nullable();
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('type', ['academic', 'administrative', 'services'])->default('academic');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('set null');
            $table->index(['tenant_id', 'code']);
        });

        Schema::create('academic_terms', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('tenant_id');
            $table->string('school_year'); // e.g. '2026-2027'
            $table->string('term_name');   // e.g. '1st Semester', 'Summer'
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(false);
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index(['tenant_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('academic_terms');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('campuses');
        Schema::dropIfExists('tenants');
    }
};

