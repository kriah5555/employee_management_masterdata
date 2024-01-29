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
        Schema::create('employee_switch_plannings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_from')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->foreignId('request_to')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->foreignId('plan_id')->nullable()->references('id')->on('planning_base')->onDelete('cascade');
            $table->tinyInteger('request_status')->nullable(); # 1 => request pending, 2-> request accepted
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
        Schema::dropIfExists('employee_switch_plannings');
    }
};
