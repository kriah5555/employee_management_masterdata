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
        Schema::create('locations_to_workstations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->references('id')->on('locations')->onDelete('cascade');
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
        Schema::dropIfExists('locations_to_workstations');
    }
};