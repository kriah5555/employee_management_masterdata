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

        Schema::create('holiday_codes', function (Blueprint $table) {
            $table->id();
            $table->string('holiday_code_name');
            $table->integer('internal_code');
            $table->string('description')->nullable();
            $table->tinyInteger('holiday_type'); # [1 => 'Paid', 2 => 'Unpaid', 3 => 'Sick Leave']
            $table->tinyInteger('count_type'); # [1 => 'Hours', 2 => 'Days', 3 => 'Sick Leave']
            $table->tinyInteger('icon_type'); # [1 => 'Illness', 2 => 'Holiday', 3 => 'Unemployed', 4 => 'Others']
            $table->tinyInteger('consider_plan_hours_in_week_hours'); # [0 => 'No', 1 => 'Yes']
            $table->tinyInteger('employee_category'); #  [1 => 'HQ servant', 2 => 'Servant', 3 => 'Worker']
            $table->tinyInteger('contract_type'); # [1 => 'Both', 2 => 'Full time', 3 => 'Part time']
            $table->tinyInteger('carry_forword'); #, [0 => 'No', 1 => 'Yes']
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('holiday_codes');
    }
};