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
        Schema::dropIfExists('dashboard_access');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('dashboard_access', function (Blueprint $table) {
            $table->id();
            $table->string('access_key');
            $table->tinyInteger('type'); # [1 => 'company', 2 => 'location']
            $table->foreignId('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->unsignedBigInteger('location_id')->nullable();
            $table->timestamps();
        });
    }
};
