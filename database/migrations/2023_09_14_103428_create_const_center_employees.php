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
        Schema::create('const_center_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cost_centers_id')->references('id')->on('cost_centers')->onDelete('cascade');
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('const_center_employees');
    }
};
