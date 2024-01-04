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
        Schema::dropIfExists('longterm_planning_timings');
        Schema::dropIfExists('longterm_planning');
        Schema::create('long_term_planning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles');
            $table->foreignId('workstation_id')->references('id')->on('workstations');
            $table->integer('function_id');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->tinyInteger('repeating_week');
            $table->boolean('auto_renew')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('long_term_planning_timings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('long_term_planning_id')->references('id')->on('long_term_planning');
            $table->tinyInteger('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('contract_hours');
            $table->tinyInteger('week_no');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('long_term_planning_timings');
        Schema::dropIfExists('long_term_planning');
        Schema::create('longterm_planning', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles');
            $table->foreignId('workstation_id')->references('id')->on('workstations');
            $table->integer('function_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->integer('repeating_week');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by');
            $table->integer('updated_by');
        });
        Schema::create('longterm_planning_timings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('longterm_planning_id')->references('id')->on('longterm_planning');
            $table->string('day');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('contract_hours');
            $table->integer('week_no');
            $table->integer('status');
            $table->softDeletes();
            $table->timestamps();
        });

    }
};
