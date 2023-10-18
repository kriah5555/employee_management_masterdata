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
        Schema::create('sector_minimum_salary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_salary_steps_id')->references('id')->on('sector_salary_steps')->onDelete('cascade');
            $table->integer('category_number');
            $table->float('salary')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_minimum_salary');
    }
};