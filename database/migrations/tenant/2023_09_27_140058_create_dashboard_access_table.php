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
        Schema::create('dashboard_access', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key');
            $table->string('type'); // 'companies' or 'locations'
            $table->foreignId('location_id')->references('id')->on('locations')->onDelete('cascade');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_access');
    }
};
