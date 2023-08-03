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
        Schema::create('sector_salary_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_salary_config_id')->references('id')->on('sector_salary_config')->onDelete('cascade');
            $table->integer('step_number')->nullable(true);
            $table->integer('from')->nullable(true);
            $table->integer('to')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_salary_steps');
    }
};
