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
        Schema::create('employee_benefits', function (Blueprint $table) {
            $table->id();
            $table->boolean('fuel_card')->default(false);
            $table->boolean('company_car')->default(false);
            $table->float('clothing_compensation')->nullable();
            $table->boolean('status')->default(true);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->longText('extra_info')->nullable();
            $table->foreignId('employee_benefits_id')->nullable()->references('id')->on('employee_benefits')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_profiles', function (Blueprint $table) {
            $table->dropColumn('employee_benefits_id');
            $table->dropColumn('extra_info');
        });
        Schema::dropIfExists('employee_benefits');
    }
};