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
        if (Schema::hasTable('employee_types') && Schema::hasTable('employee_type_categories')) {
            Schema::table('employee_types', function (Blueprint $table) {
                $table->foreignId('employee_type_categories_id')->references('id')->on('employee_type_categories')->onDelete('cascade');
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_types', function (Blueprint $table) {
            $table->dropForeign(['employee_type_categories_id']);
            $table->dropColumn('employee_type_categories_id');
            $table->dropSoftDeletes();
        });
    }
};
