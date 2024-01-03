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
        Schema::table('employee_contract_files', function (Blueprint $table) {
            $table->foreignId('planning_base_id')->nullable()->references('id')->on('planning_base')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_contract_files', function (Blueprint $table) {
            $table->dropForeign(['planning_base_id']);
        });

        Schema::table('employee_contract_files', function (Blueprint $table) {
            $table->dropColumn('planning_base_id');
        });
    }
};
