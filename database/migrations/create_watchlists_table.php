<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('watchlists', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone_number')->nullable();
            $table->string('email')->nullable();
            $table->string('id_number')->nullable();
            $table->string('photo')->nullable();
            $table->enum('type', ['visitor', 'employee', 'resident', 'contractor'])->default('visitor');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->text('reason');
            $table->text('description')->nullable();
            $table->text('actions_taken')->nullable();
            $table->date('watchlist_date')->default(now());
            $table->enum('status', ['active', 'resolved', 'archived'])->default('active');
            $table->unsignedBigInteger('added_by')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamps();

            $table->foreign('added_by')->references('id')->on('users')->onDelete('set null');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');

            $table->index('phone_number');
            $table->index('priority');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('watchlists');
    }
};