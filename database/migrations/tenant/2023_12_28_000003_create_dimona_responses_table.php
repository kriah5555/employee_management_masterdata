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
        Schema::create('dimona_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimona_base_id')->references('id')->on('dimona_base')->onDelete('cascade');
            $table->string('result');
            $table->string('dimona_period_id');
            $table->string('registration_id')->nullable();
            $table->longText('smals_response')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimona_responses');
    }
};
