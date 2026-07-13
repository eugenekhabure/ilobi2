<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('target_groups')->nullable();
            $table->enum('channel', ['whatsapp', 'sms', 'both'])->default('whatsapp');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('facility_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_templates');
    }
};