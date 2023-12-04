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
        Schema::create('availability', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->nullable()->references('id')->on('employee_profiles');
            $table->unsignedBigInteger('company_id'); // Store company_id directly
            $table->integer('type')->nullable(false);
            $table->integer('year')->nullable(false);
            $table->integer('month')->nullable(false);
            $table->json('dates')->nullable(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('availability');
    }
};