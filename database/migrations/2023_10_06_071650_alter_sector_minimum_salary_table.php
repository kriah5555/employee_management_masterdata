<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sector_minimum_salary', function (Blueprint $table) {
            // Add the 'monthly_minimum_salary' and 'hourly_minimum_salary' fields
            $table->renameColumn('salary', 'hourly_minimum_salary');
            $table->float('monthly_minimum_salary')->nullable();
        });
        Schema::table('sector_minimum_salary_backup', function (Blueprint $table) {
            $table->tinyInteger('salary_type')->default(1); # [1 => 'monthly', 2 => 'hourly']
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sector_minimum_salary', function (Blueprint $table) {
            // Reverse the changes - add back the 'salary' field
            $table->renameColumn('hourly_minimum_salary', 'salary');

            // Remove the 'monthly_minimum_salary' and 'hourly_minimum_salary' fields
            $table->dropColumn('monthly_minimum_salary');
        });
        Schema::table('sector_minimum_salary_backup', function (Blueprint $table) {
            $table->dropColumn('salary_type');
        });
    }
};