<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_acknowledgments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_alert_id')->constrained()->onDelete('cascade');
            $table->foreignId('alert_recipient_id')->constrained()->onDelete('cascade');
            $table->string('acknowledged_by_type');
            $table->unsignedBigInteger('acknowledged_by_id');
            $table->timestamp('acknowledged_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();

            $table->unique(['emergency_alert_id', 'alert_recipient_id'], 'unique_alert_ack');
            $table->index('emergency_alert_id');
            $table->index('acknowledged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_acknowledgments');
    }
};