<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('amenities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->string('location')->nullable();
            $table->integer('capacity')->default(10);
            $table->integer('max_booking_days')->default(7);
            $table->integer('advance_notice_hours')->default(2);
            $table->decimal('price', 10, 2)->default(0);
            $table->boolean('requires_approval')->default(true);
            $table->boolean('is_active')->default(true);
            $table->json('operating_hours')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index('facility_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('amenities');
    }
};