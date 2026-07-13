<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('package_name');
            $table->decimal('mrp', 10, 2)->default(0);
            $table->decimal('amount', 10, 2)->default(0);
            $table->enum('subscription_type', ['daily', 'monthly', 'yearly'])->default('monthly');
            $table->integer('days')->default(30);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};