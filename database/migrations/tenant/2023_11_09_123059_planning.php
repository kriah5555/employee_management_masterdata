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
        Schema::create('planning_base', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->foreignId('workstation_id')->references('id')->on('workstations');
            $table->integer('function_id');
            $table->timestamp('start_date_time');
            $table->timestamp('end_date_time');
            $table->decimal('contract_hours');
            $table->integer('employee_type_id');
            $table->integer('plan_type'); //OTH, front end, cloned, mobile app, event planning, openshift.
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles');
            $table->integer('mail_status')->default(0);
            $table->integer('contract_status')->default(0);
            $table->integer('dimona_status')->default(0);
            $table->integer('status')->default(0);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });

        Schema::create('event_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->date('event_start_date');
            $table->date('event_end_date');
            $table->string('extra_info', 750)->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by');
            $table->integer('updated_by');
        });

        Schema::create('event_department_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_details_id')->references('id')->on('event_details');
            $table->foreignId('workstation_id')->references('id')->on('workstations');
            $table->integer('function_id');
            $table->time('start_time');
            $table->time('end_time');
            $table->string('extra_info', 750)->nullable();
            $table->integer('employee_count')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by');
            $table->integer('updated_by');
        });

        Schema::create('event_employee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_department_id')->references('id')->on('event_department_details');
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles');
            $table->integer('request_status'); // Pending, Accepted, Rejected.
            $table->integer('request_by');
            $table->integer('responded_by');
            $table->timestamp('request_at');
            $table->timestamp('responded_at');
            $table->integer('status')->default(1);
            $table->integer('plan_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('vacancies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->references('id')->on('locations');
            $table->integer('workstation_id');
            $table->integer('function')->default(0);
            $table->integer('employee_type')->default(0);
            $table->timestamp('start_date_time');
            $table->timestamp('end_date_time');
            $table->integer('vacancy_count');
            $table->integer('approval_type');
            $table->string('extra_info', 750)->nullable();
            $table->integer('status');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by');
            $table->integer('updated_by');
        });

        Schema::create('vacancy_post_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vacancy_id')->references('id')->on('vacancies');
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles');
            $table->integer('request_status'); // Pending, Accepted, Rejected.
            $table->integer('request_by');
            $table->integer('responded_by');
            $table->timestamp('request_at');
            $table->timestamp('responded_at');
            $table->integer('status')->default(1);
            $table->integer('plan_id');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('time_registration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->references('id')->on('planning_base');
            $table->timestamp('actual_start_time');
            $table->timestamp('actual_end_time')->nullable();
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('start_reason_id');
            $table->integer('stop_reason_id');
            $table->integer('started_by');
            $table->integer('ended_by');
        });

        Schema::create('overtime', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_registration_id')->references('id')->on('time_registration');
            $table->integer('overtime_type');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by');
            $table->integer('updated_by');
        });

        Schema::create('planning_break', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->references('id')->on('planning_base');
            $table->timestamp('break_start_time');
            $table->timestamp('break_end_time');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('started_by');
            $table->integer('ended_by');
        });

        Schema::create('planning_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')->references('id')->on('planning_base');
            $table->foreignId('file_id')->references('id')->on('files');
            $table->integer('contract_status');
            $table->string('signed_type');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by');
            $table->integer('updated_by');
        });

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('longterm_planning_timings');
        Schema::dropIfExists('longterm_planning');
        Schema::dropIfExists('planning_contracts');
        Schema::dropIfExists('planning_break');
        Schema::dropIfExists('overtime');
        Schema::dropIfExists('time_registration');
        Schema::dropIfExists('vacancy_post_employee');
        Schema::dropIfExists('vacancies');
        Schema::dropIfExists('event_employee_details');
        Schema::dropIfExists('event_department_details');
        Schema::dropIfExists('event_details');
        Schema::dropIfExists('planning_base');
    }
};
