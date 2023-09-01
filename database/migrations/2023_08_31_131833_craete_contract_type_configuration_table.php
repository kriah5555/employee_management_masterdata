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
        Schema::create('contract_type_config', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_active')->default(true);
            $table->boolean('status')->default(true);
            $table->foreign('contract_type_id')->references('id')->on('contract_types')->onDelete('cascade');
            $table->foreign('contract_type_employee_type_id')->references('id')->on('contract_type_employee_type')->onDelete('cascade');
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);  
            $table->timestamps();
            $table->softDeletes();
    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_type_configs');
    }
};
