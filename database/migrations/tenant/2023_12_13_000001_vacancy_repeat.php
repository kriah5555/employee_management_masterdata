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

        if (schema::hasTable('vacancies') && Schema::hasColumns('vacancies', ['employee_type', 'workstation_id', 'function_id']) ) {
            Schema::table('vacancies', function (Blueprint $table) {
                $table->dropColumn(['employee_type', 'workstation_id', 'function_id']);
            });
        }

        if (schema::hasTable('vacancy_post_employee') && Schema::hasColumns('vacancy_post_employee', ['request_by']) ) {
            Schema::table('vacancy_post_employee', function (Blueprint $table) {
                $table->dropColumn(['request_by']);
            });
        }

        Schema::create('vacancy_repeat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->references('id')->on('vacancies');
            $table->integer('repeat_type'); // Daily, Weekly Monthly.
            $table->integer('repeat_end_date');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });

        // Schema::create('vacancy_workstations', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('vacancy_id')->references('id')->on('vacancies');
        //     $table->foreignId('workstation_id')->references('id')->on('workstations');
        //     $table->integer('status');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        // Schema::create('vacancy_functions', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('vacancy_id')->references('id')->on('vacancies');
        //     $table->integer('functions_id');
        //     $table->integer('status');
        //     $table->timestamps();
        //     $table->softDeletes();
        // });

        Schema::create('vacancy_employee_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->references('id')->on('vacancies');
            $table->integer('employee_types_id');
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vacancy_repeat');
        // Schema::dropIfExists('vacancy_workstations');
        // Schema::dropIfExists('vacancy_functions');
        Schema::dropIfExists('vacancy_employee_types');
    }
};
