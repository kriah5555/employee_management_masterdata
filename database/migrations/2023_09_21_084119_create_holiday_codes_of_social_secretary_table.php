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
        Schema::create('holiday_codes_of_social_secretary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('holiday_code_id')->nullable()->references('id')->on('holiday_codes')->onDelete('cascade');
            $table->foreignId('social_secretary_id')->nullable()->references('id')->on('social_secretaries')->onDelete('cascade');
            $table->string('social_secretary_code')->default("");
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('holiday_codes_of_social_secretary');
    }
};
