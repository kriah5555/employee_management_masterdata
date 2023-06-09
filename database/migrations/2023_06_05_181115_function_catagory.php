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
        if (!Schema::hasTable('function_catagory')) {
            Schema::create('function_catagory', function (Blueprint $table) {
                $table->id('function_catagory_id');
                $table->foreignId('sector_id')->references('sector_id')->on('sectors');
                $table->string('function_catagory_name');
                $table->longText('description')->nullable(TRUE);
                $table->integer('status')->default(1);
                $table->timestamps();
                $table->integer('created_by');
                $table->integer('updated_by');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('function_catagory');
    }
};
