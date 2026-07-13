<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained()->onDelete('cascade');
            $table->foreignId('visiting_detail_id')->constrained()->onDelete('cascade');
            $table->foreignId('visitor_id')->constrained()->onDelete('cascade');
            $table->foreignId('host_person_id')->nullable()->constrained('people')->onDelete('set null');
            $table->integer('rating')->default(5);
            $table->text('comment')->nullable();
            $table->integer('host_rating')->nullable();
            $table->integer('security_rating')->nullable();
            $table->integer('cleanliness_rating')->nullable();
            $table->integer('overall_rating')->nullable();
            $table->boolean('would_recommend')->default(true);
            $table->json('response')->nullable();
            $table->timestamp('submitted_at')->useCurrent();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_flagged')->default(false);
            $table->string('flag_reason')->nullable();
            $table->timestamps();

            $table->index('facility_id');
            $table->index('visiting_detail_id');
            $table->index('rating');
            $table->index('submitted_at');
            $table->index('is_flagged');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};