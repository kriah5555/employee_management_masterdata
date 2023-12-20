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
        Schema::create('employee_contract_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->foreignId('employee_contract_id')->nullable()->references('id')->on('employee_contract')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->references('id')->on('files')->onDelete('cascade');
            $table->tinyInteger('contract_status')->default(1); # [1 => unsigned, 2 => signed, 3 => approved] 
            $table->boolean('status')->default(true); # signed, unsigned 
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_contract_files');
    }
};
