<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sector_age_salary', function (Blueprint $table) {
            $table->time('max_time_to_work')->default('00:00'); // Add new column
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sector_age_salary', function (Blueprint $table) {
            $table->dropColumn('max_time_to_work'); // Drop the column if rolling back the migration
        });
    }
};
