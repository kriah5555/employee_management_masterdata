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
        Schema::create('company_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->references('id')->on('rules')->onDelete('cascade');
            $table->foreignId('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->string('value');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_rules');
    }
};