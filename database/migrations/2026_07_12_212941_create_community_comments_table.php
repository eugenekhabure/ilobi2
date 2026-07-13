<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('community_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('community_posts')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('people')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('community_comments')->onDelete('cascade');
            $table->text('content');
            $table->string('media')->nullable();
            $table->boolean('is_approved')->default(true);
            $table->timestamps();

            $table->index('post_id');
            $table->index('author_id');
            $table->index('parent_id');
            $table->index('is_approved');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('community_comments');
    }
};