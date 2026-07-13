<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('access_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->morphs('loggable'); // Links to people, visitors, or deliveries
            $table->enum('action', ['check_in', 'check_out', 'entry_granted', 'entry_denied']);
            $table->foreignId('access_zone_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('performed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->json('details')->nullable(); // Extra data like QR code scanned, gate number, etc.
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_logs');
    }
};