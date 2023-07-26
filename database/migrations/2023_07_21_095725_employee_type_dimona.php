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
        Schema::create('employee_type_dimonas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_type_id')->references('id')->on('employee_types')->onDelete('cascade');
            $table->foreignId('dimona_type_id')->references('id')->on('dimona_types')->onDelete('cascade');
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_type_dimonas');
    }
};
