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
        Schema::create('employee_holiday_count_reasons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_holiday_count_id')->nullable()->references('id')->on('employee_holiday_count')->onDelete('cascade');
            $table->decimal('count', 5, 2)->default(0.00); # 5 total digits, 2 decimal places, default 0.00
            $table->string('reason')->nullable();
            $table->tinyInteger('count_type'); # [1 => 'Hours', 2 => 'Days', 3 => 'Sick Leave']
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
        Schema::dropIfExists('employee_holiday_count_reasons');
    }
};