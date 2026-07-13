<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->enum('brand', ['zkteco', 'hikvision', 'generic']);
            $table->string('device_ip')->nullable();
            $table->integer('device_port')->default(8080);
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('api_key')->nullable();
            $table->integer('door_number')->default(1);
            $table->enum('status', ['online', 'offline', 'error'])->default('offline');
            $table->json('settings')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_devices');
    }
};