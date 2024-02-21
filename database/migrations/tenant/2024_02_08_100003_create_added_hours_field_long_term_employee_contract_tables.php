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
        Schema::table('long_term_employee_contract', function (Blueprint $table) {
            $table->string('reserved_hours')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('long_term_employee_contract', function (Blueprint $table) {
            $table->dropColumn('reserved_hours');
        });
    }
};
