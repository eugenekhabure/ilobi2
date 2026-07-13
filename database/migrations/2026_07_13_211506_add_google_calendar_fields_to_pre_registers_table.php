<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pre_registers', function (Blueprint $table) {
            $table->string('google_event_id')->nullable();
            $table->string('google_event_link')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pre_registers', function (Blueprint $table) {
            $table->dropColumn(['google_event_id', 'google_event_link']);
        });
    }
};