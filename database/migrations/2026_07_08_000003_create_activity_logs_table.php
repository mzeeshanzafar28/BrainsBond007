<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('work_sessions')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // admin tenant
            $table->enum('type', ['screenshot', 'webcam', 'keystroke_summary', 'app_usage', 'idle_alert', 'location_check']);
            $table->string('file_path')->nullable(); // for screenshots/webcam images
            $table->json('metadata')->nullable(); // flexible data per type
            $table->timestamp('captured_at');
            $table->timestamps();

            $table->index(['employee_id', 'type']);
            $table->index(['session_id', 'captured_at']);
            $table->index(['user_id', 'captured_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
