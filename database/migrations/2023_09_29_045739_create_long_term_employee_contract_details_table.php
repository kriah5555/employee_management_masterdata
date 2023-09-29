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
        Schema::create('long_term_employee_contract_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_contract_details_id')->references('id')->on('employee_contract_details')->onDelete('cascade');
            $table->string('sub_type');
            $table->string('schedule_type');
            $table->string('employement_type');
            $table->float('weekly_contract_hours')->nullable();
            $table->smallInteger('work_days_per_week')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('long_term_employee_contract_details');
    }
};