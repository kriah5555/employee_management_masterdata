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
        Schema::create('workstation_to_funcitons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('function_title_id')->references('id')->on('function_titles')->onDelete('cascade');
            $table->foreignId('workstation_id')->references('id')->on('workstations')->onDelete('cascade');
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
        Schema::dropIfExists('workstation_to_funcitons');
    }
};