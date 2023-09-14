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
        Schema::table('holiday_codes', function (Blueprint $table) {
             // Drop the existing employee_category column
            $table->dropColumn('employee_category');
        });

        Schema::table('holiday_codes', function (Blueprint $table) {
           $table->json('employee_category')->nullable();
       });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holiday_codes', function (Blueprint $table) {
            $table->json('employee_category');
        });
        
        Schema::table('holiday_codes', function (Blueprint $table) {
            $table->dropColumn('employee_category');
      });
    }
};
