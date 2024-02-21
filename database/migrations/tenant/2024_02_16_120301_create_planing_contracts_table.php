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
        Schema::table('employee_contract_files', function (Blueprint $table) {
            $table->dropColumn('planning_base_id');
            $table->unsignedBigInteger('contract_type_id')->nullable();
        });

        Schema::dropIfExists('planning_contracts');

        Schema::create('planning_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('planning_base_id')->nullable()->references('id')->on('planning_base')->onDelete('cascade');
            $table->foreignId('file_id')->nullable()->references('id')->on('files')->onDelete('cascade');
            $table->unsignedBigInteger('contract_type_id')->nullable();
            $table->tinyInteger('contract_status')->default(1); # [1 => unsigned, 2 => signed, 3 => approved] 
            $table->boolean('status')->default(true); # signed, unsigned 
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
        Schema::table('employee_contract_files', function (Blueprint $table) {
            $table->foreignId('planning_base_id')->nullable()->references('id')->on('planning_base')->onDelete('cascade');
            $table->dropColumn('contract_type_id');
        });
        Schema::dropIfExists('planning_contracts');
    }
};
