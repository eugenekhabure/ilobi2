<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained('staff_departments')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('position');
            $table->text('bio')->nullable();
            $table->string('photo')->nullable();
            $table->enum('status', ['active', 'inactive', 'on_leave'])->default('active');
            $table->boolean('is_emergency_contact')->default(false);
            $table->json('working_hours')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('facility_id');
            $table->index('department_id');
            $table->index('status');
            $table->index('is_emergency_contact');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};