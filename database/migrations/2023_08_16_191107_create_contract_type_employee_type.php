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
        Schema::create('contract_type_employee_type', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_type_id')->references('id')->on('contract_types')->onDelete('cascade');
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_type_employee_type');
    }
};
