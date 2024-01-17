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
        Schema::create('employee_id_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->references('id')->on('files')->onDelete('cascade');
            $table->tinyInteger('type')->nullable(); # [1 => ID front, 3 => ID back] 
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_id_cards');
    }
};
