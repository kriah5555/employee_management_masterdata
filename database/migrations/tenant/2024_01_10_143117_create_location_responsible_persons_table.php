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
        Schema::create('location_responsible_persons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->references('id')->on('locations')->onDelete('cascade');
            $table->timestamps();
        });
        Schema::table('locations', function (Blueprint $table) {
            $table->dropColumn('responsible_person_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('location_responsible_persons');
        Schema::table('locations', function (Blueprint $table) {
            $table->unsignedBigInteger('responsible_person_id')->nullable();
        });
    }
};
