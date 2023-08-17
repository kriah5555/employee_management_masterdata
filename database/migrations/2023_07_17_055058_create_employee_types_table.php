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
        if (!Schema::hasTable('employee_types')) {
            Schema::create('employee_types', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->longText('description')->nullable(true);
                $table->boolean('status')->default(true);
                $table->integer('created_by')->nullable(true);
                $table->integer('updated_by')->nullable(true);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_types');
    }
};
