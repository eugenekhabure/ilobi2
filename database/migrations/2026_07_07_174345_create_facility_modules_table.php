<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facility_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->string('module_key'); // e.g., 'employees', 'residents', 'deliveries', 'vehicles', 'invitations'
            $table->boolean('is_enabled')->default(true);
            $table->timestamps();

            $table->unique(['facility_id', 'module_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('facility_modules');
    }
};