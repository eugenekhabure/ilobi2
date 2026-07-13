<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facial_recognition_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['visitor', 'employee', 'resident', 'unknown'])->default('unknown');
            $table->unsignedBigInteger('related_id')->nullable();
            $table->string('full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('image_path')->nullable();
            $table->float('confidence_score')->nullable();
            $table->enum('status', ['matched', 'unmatched', 'error'])->default('unmatched');
            $table->json('face_data')->nullable();
            $table->string('device_name')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facial_recognition_logs');
    }
};