<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('author_id')->constrained('people')->onDelete('cascade');
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['announcement', 'event', 'classified', 'lost_found', 'general'])->default('general');
            $table->enum('status', ['pending', 'published', 'rejected', 'archived'])->default('pending');
            $table->string('featured_image')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->timestamp('event_date')->nullable();
            $table->string('location')->nullable();
            $table->integer('view_count')->default(0);
            $table->integer('like_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->timestamps();

            $table->index('facility_id');
            $table->index('author_id');
            $table->index('type');
            $table->index('status');
            $table->index('is_featured');
            $table->index('published_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_posts');
    }
};