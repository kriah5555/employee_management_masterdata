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
        Schema::table('sector_minimum_salary', function (Blueprint $table) {
            // Remove the 'salary' field
            $table->dropColumn('salary');

            // Add the 'monthly_minimum_salary' and 'hourly_minimum_salary' fields
            $table->float('monthly_minimum_salary')->nullable();
            $table->float('hourly_minimum_salary',)->nullable();
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
            $table->float('salary')->nullable();
            
            // Remove the 'monthly_minimum_salary' and 'hourly_minimum_salary' fields
            $table->dropColumn('monthly_minimum_salary');
            $table->dropColumn('hourly_minimum_salary');
        });
        Schema::table('sector_minimum_salary_backup', function (Blueprint $table) {
            $table->dropColumn('salary_type');
        });
    }
};
