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
        Schema::create('employee_type_sector_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->references('id')->on('rules')->onDelete('cascade');
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->foreignId('sector_id')->references('id')->on('sector')->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_type_sector_rules');
    }
};
