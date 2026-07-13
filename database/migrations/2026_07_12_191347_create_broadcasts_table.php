<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->json('target_groups')->nullable(); // ['residents', 'employees', 'security', 'visitors']
            $table->enum('channel', ['whatsapp', 'sms', 'both'])->default('whatsapp');
            $table->enum('status', ['draft', 'sent', 'scheduled', 'failed'])->default('draft');
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->integer('total_recipients')->default(0);
            $table->integer('total_delivered')->default(0);
            $table->integer('total_failed')->default(0);
            $table->timestamps();

            $table->index('facility_id');
            $table->index('status');
            $table->index('channel');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcasts');
    }
};