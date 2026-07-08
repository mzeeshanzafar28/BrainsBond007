<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add SaaS fields to users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('organization_name')->nullable()->after('name');
            $table->string('plan_type')->default('free')->after('email'); // free, starter, pro, enterprise
            $table->string('timezone')->default('UTC')->after('plan_type');
            $table->timestamp('trial_ends_at')->nullable()->after('timezone');
        });

        // Add missing fields to employees table
        Schema::table('employees', function (Blueprint $table) {
            $table->integer('age')->nullable()->after('name');
            $table->string('phone', 20)->nullable()->after('email');
            $table->string('department')->nullable()->after('phone');
            $table->string('designation')->nullable()->after('department');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('allow_remote');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['organization_name', 'plan_type', 'timezone', 'trial_ends_at']);
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['age', 'phone', 'department', 'designation', 'status']);
        });
    }
};
