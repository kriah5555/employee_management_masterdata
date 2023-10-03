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
        Schema::create('employee_type_holiday_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_code_id')->references('id')->on('holiday_codes')->onDelete('cascade');
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_type_holiday_codes');
    }
};
