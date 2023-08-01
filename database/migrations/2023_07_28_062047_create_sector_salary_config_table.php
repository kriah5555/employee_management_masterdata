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
        Schema::create('sector_salary_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_id')->references('id')->on('sectors')->onDelete('cascade');
            $table->integer('category')->nullable(true);
            $table->integer('steps')->nullable(true);
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_salary_config');
    }
};
