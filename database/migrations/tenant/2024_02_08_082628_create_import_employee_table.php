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
        Schema::create('import_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('file_id')->nullable()->references('id')->on('files')->onDelete('cascade');
            $table->foreignId('feedback_file_id')->nullable()->references('id')->on('files')->onDelete('cascade');
            $table->tinyInteger('import_status')->nullable();  # 1 => pending 2 => completed
            $table->date('imported_date')->nullable(); 
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
        Schema::dropIfExists('import_employee');
    }
};
