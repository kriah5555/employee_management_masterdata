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
                $table->foreignId('sector_id')->references('sector_id')->on('sectors');
                $table->integer('age');
                $table->float('percentage');
                $table->integer('status')->default(1);
                $table->timestamps();
                $table->integer('created_by');
                $table->integer('updated_by');
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
