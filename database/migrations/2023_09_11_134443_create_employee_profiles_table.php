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
            $table->unsignedBigInteger('uid');
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->string('email');
            $table->string('phone_number');
            $table->string('social_security_number');
            $table->string('place_of_birth')->nullable();
            $table->date('date_of_joining');
            $table->date('date_of_leaving')->nullable();
            $table->string('language')->default('en');
            $table->string('dependent_spouse')->nullable();
            $table->foreignId('gender_id')->nullable()->references('id')->on('genders')->onDelete('cascade');
            $table->foreignId('marital_status_id')->nullable()->references('id')->on('marital_statuses')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->references('id')->on('bank_accounts')->onDelete('cascade');
            $table->foreignId('address_id')->nullable()->references('id')->on('address')->onDelete('cascade');
            $table->foreignId('company_id')->nullable()->references('id')->on('companies')->onDelete('cascade');
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
        Schema::dropIfExists('employee_profiles');
    }
};