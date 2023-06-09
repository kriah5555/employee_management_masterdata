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
        if (Schema::hasTable('function_titles') && Schema::hasTable('function_catagory')) {
            Schema::create('function_catagory_to_titles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('function_title_id')->references('function_title_id')->on('function_titles');
                $table->foreignId('function_catagory_id')->references('function_catagory_id')->on('function_catagory');
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
        Schema::dropIfExists('function_catagory_to_titles');
    }
};
