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
        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        // Schema::create('employee_contact_details', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
        //     $table->string('email');
        //     $table->string('secondary_email');
        //     $table->string('phone_number');
        //     $table->boolean('status')->default(true);
        //     $table->unsignedBigInteger('created_by')->nullable();
        //     $table->unsignedBigInteger('updated_by')->nullable();
        //     $table->timestamps();
        //     $table->softDeletes();
        // });
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->boolean('fuel_card')->default(false);
            $table->boolean('company_car')->default(false);
            $table->float('clothing_compensation')->nullable();
            $table->float('clothing_size')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_social_secretary_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->string('social_secretary_number')->nullable();
            $table->string('contract_number')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_contract', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->unsignedBigInteger('employee_type_id');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('long_term_employee_contract', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_contract_id')->references('id')->on('employee_contract')->onDelete('cascade');
            $table->string('sub_type');
            $table->string('schedule_type');
            $table->string('employement_type');
            $table->float('weekly_contract_hours')->nullable();
            $table->smallInteger('work_days_per_week')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_commute', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->unsignedBigInteger('commute_types');
            $table->foreignId('location_id')->nullable()->references('id')->on('locations')->onDelete('cascade');
            $table->float('distance')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_salary_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->string('salary');
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_function_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_contract_id')->references('id')->on('employee_contract')->onDelete('cascade');
            $table->unsignedBigInteger('function_id');
            $table->foreignId('salary')->nullable()->references('id')->on('employee_salary_details')->onDelete('cascade');
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
        Schema::table('employee_function_details', function (Blueprint $table) {
            $table->dropForeign(['employee_contract_id']);
            $table->dropForeign(['function_title_id']);
            $table->dropForeign(['salary_id']);
        });

        Schema::table('employee_commute', function (Blueprint $table) {
            $table->dropForeign(['employee_profile_id']);
            $table->dropForeign(['commute_type_id']);
            $table->dropForeign(['location_id']);
        });

        Schema::table('employee_contract', function (Blueprint $table) {
            $table->dropForeign(['employee_profile_id']);
            $table->dropForeign(['employee_type_id']);
        });

        Schema::table('long_term_employee_contract', function (Blueprint $table) {
            $table->dropForeign(['employee_contract_id']);
        });

        Schema::table('employee_benefits', function (Blueprint $table) {
            $table->dropForeign(['employee_profile_id']);
        });

        Schema::table('employee_social_secretary_details', function (Blueprint $table) {
            $table->dropForeign(['employee_profile_id']);
        });

        // Schema::table('employee_contact_details', function (Blueprint $table) {
        //     $table->dropForeign(['employee_profile_id']);
        // });

        Schema::table('employee_salary_details', function (Blueprint $table) {
            $table->dropForeign(['employee_profile_id']);
        });

        Schema::dropIfExists('employee_function_details');
        Schema::dropIfExists('employee_commute');
        Schema::dropIfExists('long_term_employee_contract');
        Schema::dropIfExists('employee_contract');
        Schema::dropIfExists('employee_benefits');
        Schema::dropIfExists('employee_social_secretary_details');
        // Schema::dropIfExists('employee_contact_details');
        Schema::dropIfExists('employee_salary_details');
        Schema::dropIfExists('employee_profiles');
    }
};
