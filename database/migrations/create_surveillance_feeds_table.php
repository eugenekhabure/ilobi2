<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surveillance_feeds', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location')->nullable();
            $table->string('camera_url');
            $table->string('stream_url')->nullable();
            $table->string('camera_type')->default('ip');
            $table->string('brand')->nullable();
            $table->string('model')->nullable();
            $table->string('ip_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->enum('status', ['online', 'offline', 'recording', 'error'])->default('offline');
            $table->boolean('is_recording')->default(false);
            $table->string('recording_path')->nullable();
            $table->integer('storage_limit_days')->default(30);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');

            $table->index('status');
            $table->index('location');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surveillance_feeds');
    }
};