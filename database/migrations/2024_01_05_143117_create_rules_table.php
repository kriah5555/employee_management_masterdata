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
        Schema::dropIfExists('employee_type_rules');
        Schema::dropIfExists('rules');
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->smallInteger('type');
            $table->smallInteger('value_type');
            $table->string('value');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_type_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->references('id')->on('rules')->onDelete('cascade');
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->string('value');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('sector_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->references('id')->on('rules')->onDelete('cascade');
            $table->foreignId('sector_id')->references('id')->on('sectors')->onDelete('cascade');
            $table->string('value');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_type_sector_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->references('id')->on('rules')->onDelete('cascade');
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->foreignId('sector_id')->references('id')->on('sectors')->onDelete('cascade');
            $table->string('value');
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
        Schema::dropIfExists('employee_type_sector_rules');
        Schema::dropIfExists('employee_type_rules');
        Schema::dropIfExists('sector_rules');
        Schema::dropIfExists('rules');
        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->longText('description')->nullable();
            $table->smallInteger('type');
            $table->smallInteger('value_type');
            $table->string('value');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create('employee_type_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->references('id')->on('rules')->onDelete('cascade');
            $table->string('value');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
};
