<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // admin tenant
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->enum('status', ['active', 'completed', 'abandoned'])->default('active');
            $table->boolean('face_verified')->default(false);
            $table->float('face_match_score')->nullable();
            $table->boolean('location_verified')->default(false);
            $table->string('ip_address', 45)->nullable();
            $table->integer('total_active_minutes')->default(0);
            $table->integer('total_idle_minutes')->default(0);
            $table->timestamp('last_heartbeat_at')->nullable();
            $table->timestamps();

            $table->index(['employee_id', 'status']);
            $table->index(['user_id', 'started_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_sessions');
    }
};
