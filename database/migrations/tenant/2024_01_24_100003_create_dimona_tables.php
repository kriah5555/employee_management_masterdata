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
        Schema::dropIfExists('dimona_responses');
        Schema::dropIfExists('employee_contract_long_dimonas');
        Schema::dropIfExists('planning_dimonas');
        Schema::dropIfExists('dimona_errors');
        Schema::dropIfExists('dimona_details');
        Schema::dropIfExists('dimona_base');
        Schema::create('dimonas', function (Blueprint $table) {
            $table->id();
            $table->string('type'); //long_term, plan, flex_check
            $table->string('dimona_period_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('dimona_declarations', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->foreignId('dimona_id')->nullable()->references('id')->on('dimonas')->onDelete('cascade');
            $table->string('type'); //in, update, out, cancel
            $table->string('dimona_declartion_status')->default('pending'); //pending, success, failed
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('dimona_declaration_time_registration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimona_declaration_id')->nullable()->references('id')->on('dimona_declarations')->onDelete('cascade');
            $table->foreignId('time_registration_id')->nullable()->references('id')->on('time_registrations')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('dimona_declaration_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimona_declaration_id')->nullable()->references('id')->on('dimona_declarations')->onDelete('cascade');
            $table->unsignedBigInteger('dimona_error_code_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('planning_dimonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_id')->nullable()->references('id')->on('planning_base')->onDelete('cascade');
            $table->foreignId('dimona_id')->nullable()->references('id')->on('dimonas')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('long_term_dimonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_contract_id')->nullable()->references('id')->on('employee_contract')->onDelete('cascade');
            $table->foreignId('dimona_id')->nullable()->references('id')->on('dimonas')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('long_term_employee_contract', function (Blueprint $table) {
            $table->string('dimona_period_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('long_term_dimonas');
        Schema::dropIfExists('planning_dimonas');
        Schema::dropIfExists('dimona_declaration_errors');
        Schema::dropIfExists('dimona_declarations');
        Schema::dropIfExists('dimonas');
        Schema::create('dimona_base', function (Blueprint $table) {
            $table->id();
            $table->uuid('unique_id')->nullable();
            $table->string('dimona_code');
            $table->string('dimona_channel')->default('rest');
            $table->string('employee_id');
            $table->string('employee_rsz');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
        Schema::create('dimona_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimona_base_id')->references('id')->on('dimona_base')->onDelete('cascade');
            $table->string('dimona_type');
            $table->integer('status')->default(1);
            $table->timestamp('start_date_time')->nullable();
            $table->timestamp('end_date_time')->nullable();
            $table->decimal('hours')->nullable();
            $table->timestamps();
        });
        Schema::create('dimona_errors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimona_details_id')->references('id')->on('dimona_details')->onDelete('cascade');
            $table->string('type')->default('error');
            $table->string('error_code');
            $table->timestamps();
        });
        Schema::create('planning_dimonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_base_id')->references('id')->on('planning_base')->onDelete('cascade');
            $table->foreignId('dimona_base_id')->references('id')->on('dimona_base')->onDelete('cascade');
            $table->foreignId('time_registration_id')->nullable()->references('id')->on('time_registration')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_contract_long_dimonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_contract_id')->references('id')->on('employee_contract')->onDelete('cascade');
            $table->foreignId('dimona_base_id')->references('id')->on('dimona_base')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('dimona_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dimona_details_id')->references('id')->on('dimona_details')->onDelete('cascade');
            $table->string('result');
            $table->string('dimona_period_id');
            $table->string('registration_id')->nullable();
            $table->longText('smals_response')->nullable();
            $table->timestamps();
        });
        Schema::table('long_term_employee_contract', function (Blueprint $table) {
            $table->dropColumn('dimona_period_id');
        });
    }
};
