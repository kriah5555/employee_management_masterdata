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
        Schema::create('employee_type_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->smallInteger('consecutive_days_limit');
            $table->string('icon_color');
            $table->boolean('start_in_past');
            $table->boolean('counters');
            $table->boolean('contract_hours_split');
            $table->boolean('leave_access');
            $table->boolean('holiday_access');
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('employee_type_dimona_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->foreignId('dimona_type_id')->references('id')->on('dimona_types')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_type_config');
        Schema::dropIfExists('employee_type_dimona_config');
    }
};