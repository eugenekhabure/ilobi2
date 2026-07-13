<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('category_id')->constrained('maintenance_categories')->onDelete('cascade');
            $table->foreignId('requested_by')->constrained('people')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('people')->onDelete('set null');
            $table->string('title');
            $table->text('description');
            $table->string('unit_number')->nullable();
            $table->string('block_name')->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'emergency'])->default('medium');
            $table->enum('status', ['pending', 'assigned', 'in_progress', 'completed', 'cancelled'])->default('pending');
            $table->string('photo')->nullable();
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();

            $table->index('facility_id');
            $table->index('category_id');
            $table->index('status');
            $table->index('priority');
            $table->index('requested_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_requests');
    }
};