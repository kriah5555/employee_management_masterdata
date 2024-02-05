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
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->foreignId('contract_type_id')->references('id')->on('contract_types')->onDelete('cascade');
            $table->dropColumn('employee_type_id');
            $table->dropColumn('language');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->dropColumn('contract_type_id');
            $table->foreignId('employee_type_id')->nullable()->references('id')->on('employee_types')->onDelete('cascade');
            $table->string('language')->default('en');
        });
    }
};
