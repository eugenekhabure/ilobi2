<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->foreignId('facility_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('host_person_id')->nullable()->constrained('people')->onDelete('set null');
            $table->foreignId('destination_sub_unit_id')->nullable()->constrained('sub_units')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('visitors', function (Blueprint $table) {
            $table->dropForeign(['facility_id']);
            $table->dropForeign(['host_person_id']);
            $table->dropForeign(['destination_sub_unit_id']);
            $table->dropColumn(['facility_id', 'host_person_id', 'destination_sub_unit_id']);
        });
    }
};