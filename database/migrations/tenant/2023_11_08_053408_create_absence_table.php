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
        Schema::create('absence', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('shift_type')->nullable(); #  [1 => Holiday, 2 -> Leave]
            $table->tinyInteger('duration_type')->nullable(); #  [1 => first half, 2 => second half, 3 => full day, 4 => multiple codes or combination]
            $table->tinyInteger('absence_status')->nullable(); #  [1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled]
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->foreignId('manager_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->string('reason')->nullable();
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('absence_hours', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('holiday_code_id');
            $table->foreignId('absence_id')->nullable()->references('id')->on('absence')->onDelete('cascade');
            $table->double('hours')->default(0);
            $table->tinyInteger('duration_type')->nullable(); #  [1 => first half, 2 => second half, 3 => full day, 4 => multiple codes or combination]
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('absence_dates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('absence_id')->nullable()->references('id')->on('absence')->onDelete('cascade');
            $table->json('dates')->nullable();
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
        Schema::dropIfExists('absence');
        Schema::dropIfExists('absence_codes');
        Schema::dropIfExists('absence_dates');
    }
};
