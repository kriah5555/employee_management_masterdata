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
        Schema::table('holiday_codes', function (Blueprint $table) {
            $table->dropColumn('carry_forword');
            $table->decimal('count', 5, 2)->default(0.00); # 5 total digits, 2 decimal places, default 0.00
        });
        Schema::dropIfExists('holiday_code_count');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('holiday_codes', function (Blueprint $table) {
            $table->tinyInteger('carry_forword')->default(1); // Recreate the removed field if you ever need it
            $table->dropColumn('count');
        });
        Schema::create('holiday_code_count', function (Blueprint $table) {
            $table->id();
            $table->integer('count')->default(0); # always in hours
            $table->foreignId('holiday_code_id')->constrained('holiday_codes')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
