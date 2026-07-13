<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('emergency_alerts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('severity', ['warning', 'critical', 'emergency'])->default('warning');
            $table->enum('status', ['draft', 'sent', 'failed', 'expired'])->default('draft');
            $table->json('target_audience')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('total_acknowledged')->default(0);
            $table->timestamps();

            $table->index('facility_id');
            $table->index('status');
            $table->index('severity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('emergency_alerts');
    }
};