<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('maintenance_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('maintenance_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('comment');
            $table->enum('user_type', ['resident', 'staff'])->default('resident');
            $table->string('photo')->nullable();
            $table->timestamps();

            $table->index('maintenance_request_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('maintenance_comments');
    }
};