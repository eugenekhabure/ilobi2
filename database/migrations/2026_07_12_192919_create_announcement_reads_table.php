<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('announcement_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->string('reader_type'); // App\Models\User, App\Models\Person, etc.
            $table->unsignedBigInteger('reader_id');
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['announcement_id', 'reader_type', 'reader_id']);
            $table->index('announcement_id');
            $table->index('read_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('announcement_reads');
    }
};