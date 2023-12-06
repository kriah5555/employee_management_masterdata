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
        Schema::create('contract_configurations', function (Blueprint $table) {
            $table->id();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('employee_type_id')->nullable();
            $table->foreignId('location_id')->nullable()->references('id')->on('locations')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('availability_remarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->references('id')->on('employee_profiles');
            $table->unsignedBigInteger('company_id'); // Store company_id directly
            $table->json('dates')->nullable(false);
            $table->integer('type');
            $table->string('remark')->nullable(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_configurations');
        Schema::dropIfExists('availability_remarks');
    }
};
