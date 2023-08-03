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
        if (Schema::hasTable('sectors') && !Schema::hasTable('sector_age_salary')) {
            Schema::create('sector_age_salary', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sector_id')->references('id')->on('sectors')->onDelete('cascade');
                $table->integer('age');
                $table->float('percentage')->nullable(true);
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->integer('created_by')->nullable(true);
                $table->integer('updated_by')->nullable(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_age_salary', function (Blueprint $table) {
            $table->dropForeign('sector_id');
        });
    }
};
