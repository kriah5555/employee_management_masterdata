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
        DB::table('contract_templates')->truncate();
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->unsignedBigInteger('contract_type_id');
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
            $table->unsignedBigInteger('employee_type_id')->nullable();
            $table->string('language')->default('en');
        });
    }
};
