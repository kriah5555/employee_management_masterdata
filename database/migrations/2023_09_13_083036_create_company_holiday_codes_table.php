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
        Schema::create('company_holiday_codes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->foreignId('holiday_code_id')->references('id')->on('holiday_codes')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void    
    {
        Schema::dropIfExists('company_holiday_codes');
    }
};
