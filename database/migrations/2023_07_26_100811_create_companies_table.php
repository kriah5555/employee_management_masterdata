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
        if (!Schema::hasTable('companies')) {

            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->string('company_name');
                $table->string('street')->nullable();
                $table->integer('postal_code')->nullable();
                $table->string('city')->nullable();
                $table->string('country')->nullable();
                $table->string('status')->default(true);
                $table->string('logo')->nullable();
                $table->integer('created_by')->nullable(true);
                $table->integer('updated_by')->nullable(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
