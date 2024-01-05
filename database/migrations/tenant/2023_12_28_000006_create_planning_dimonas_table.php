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
        Schema::create('planning_dimonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_base_id')->references('id')->on('planning_base')->onDelete('cascade');
            $table->foreignId('dimona_base_id')->references('id')->on('dimona_base')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planning_dimonas');
    }
};
