<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');  
            $table->string('name');
            $table->string('email')->unique();
            $table->json('face_images');
            $table->string('cnic');
            $table->time('start_working_hour');
            $table->time('end_working_hour');
            $table->boolean('allow_remote')->default(false);  // Default to false if not set
            $table->json('remote_locations')->nullable();  // Nullable as locations may not be set
            $table->boolean('is_seized')->default(false);  // Default to false
            $table->json('screenshots')->nullable();  // Nullable for screenshots
            $table->timestamps();

            $table->index('user_id');  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
