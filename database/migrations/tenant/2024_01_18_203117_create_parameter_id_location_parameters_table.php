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
        Schema::table('location_parameters', function (Blueprint $table) {
            $table->unsignedBigInteger('parameter_id')->nullable();
            $table->dropColumn('parameter_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('location_parameters', function (Blueprint $table) {
            $table->dropColumn('parameter_id');
            $table->string('parameter_name');
        });
    }
};
