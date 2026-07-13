<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blacklists', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('id_number')->nullable();
            $table->string('photo')->nullable();
            $table->enum('type', ['visitor', 'employee', 'resident', 'contractor'])->default('visitor');
            $table->text('reason');
            $table->text('description')->nullable();
            $table->date('blacklisted_date')->default(now());
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['active', 'expired', 'removed'])->default('active');
            $table->text('removal_reason')->nullable();
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('removed_by')->nullable();
            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('removed_by')->references('id')->on('users')->onDelete('set null');

            $table->index('phone_number');
            $table->index('id_number');
            $table->index('status');
            $table->index('type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blacklists');
    }
};