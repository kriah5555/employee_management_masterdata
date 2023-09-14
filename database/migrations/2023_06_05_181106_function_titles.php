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
        Schema::create('function_titles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('function_code');
            $table->longText('description')->nullable();
            $table->boolean('status')->default(true);
            $table->foreignId('function_category_id')->nullable()->references('id')->on('function_category')->onDelete('cascade');
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('function_titles');
    }
};
