<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->string('id')->primary(); // slug e.g. super-admin, tenant-admin, hr-officer
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_system')->default(true);
            $table->json('permissions')->nullable();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('name');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['avatar', 'last_login_at']);
        });

        Schema::dropIfExists('roles');
    }
};

