<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('host_person_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->onDelete('set null');
            $table->string('visitor_email')->nullable();
            $table->string('visitor_phone')->nullable();
            $table->foreignId('sub_unit_id')->nullable()->constrained()->onDelete('set null');
            $table->string('qr_code')->unique();
            $table->enum('status', ['pending', 'checked_in', 'checked_out', 'expired'])->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('checked_in_at')->nullable();
            $table->timestamp('checked_out_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};