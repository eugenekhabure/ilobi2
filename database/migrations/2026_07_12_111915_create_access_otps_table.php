<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_otps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('person_id')->constrained()->onDelete('cascade');
            $table->foreignId('invitation_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('visitor_id')->nullable()->constrained()->onDelete('set null');
            $table->string('otp_code', 10);
            $table->timestamp('expires_at');
            $table->timestamp('used_at')->nullable();
            $table->integer('attempts')->default(0);
            $table->enum('status', ['active', 'used', 'expired', 'blocked'])->default('active');
            $table->timestamps();

            $table->index('otp_code');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_otps');
    }
};