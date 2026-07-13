<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deliveries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->string('courier_name')->nullable();
            $table->string('tracking_number')->nullable();
            $table->foreignId('recipient_person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('sub_unit_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'received', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deliveries');
    }
};