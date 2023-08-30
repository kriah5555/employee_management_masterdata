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
        Schema::create('sector_minimum_salary_backup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sector_salary_config_id')->references('id')->on('sector_salary_config')->onDelete('cascade');
            $table->integer('category');
            $table->integer('revert_count');
            $table->json('salary_data');
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);  
            $table->boolean('status')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_minimum_salary_backup');
    }
};
