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
        Schema::table('employee_type_categories', function (Blueprint $table) {
            $table->boolean('sub_category_types')->default(false); // Servant or worker is required or not
            $table->boolean('schedule_types')->default(false); // Fixed or flexible schedule options is required or not
            $table->boolean('employement_types')->default(false); // Part time or full time options is required or not
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_type_categories', function (Blueprint $table) {
            $table->dropColumn('sub_category_types');
            $table->dropColumn('schedule_types');
            $table->dropColumn('employement_types');
        });
    }
};