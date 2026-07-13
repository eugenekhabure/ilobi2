<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->foreignId('person_id')->constrained('people')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['post_id', 'person_id']);
            $table->index('post_id');
            $table->index('person_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_likes');
    }
};