<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emergency_alert_id')->constrained()->onDelete('cascade');
            $table->string('recipient_type');
            $table->unsignedBigInteger('recipient_id');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('channel', ['whatsapp', 'sms', 'email'])->default('whatsapp');
            $table->enum('status', ['pending', 'sent', 'failed', 'delivered'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('emergency_alert_id');
            $table->index('status');
            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_recipients');
    }
};