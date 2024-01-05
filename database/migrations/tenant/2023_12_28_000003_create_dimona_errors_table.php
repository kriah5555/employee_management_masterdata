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
        Schema::create('dimona_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimona_details_id')->references('id')->on('dimona_details')->onDelete('cascade');
            $table->string('type')->default('error');
            $table->string('error_code');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimona_errors');
    }
};
