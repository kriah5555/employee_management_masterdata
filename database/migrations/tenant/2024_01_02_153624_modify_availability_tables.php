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
        Schema::dropIfExists('availability');
        Schema::dropIfExists('availability_remarks');
        Schema::create('employee_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles');
            $table->date('date');
            $table->boolean('availability')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_availability_remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_availability_id')->references('id')->on('employee_availabilities');
            $table->string('remark')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_availability_remarks');
        Schema::dropIfExists('employee_availabilities');
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->references('id')->on('employee_profiles');
            $table->integer('type')->nullable(false);
            $table->integer('year')->nullable(false);
            $table->integer('month')->nullable(false);
            $table->json('dates')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('availability_remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->references('id')->on('employee_profiles');
            $table->json('dates')->nullable(false);
            $table->integer('type');
            $table->string('remark')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
