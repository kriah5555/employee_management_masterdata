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
        Schema::create('company_social_secretary_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->references('id')->on('companies')->onDelete('cascade');
            $table->foreignId('social_secretary_id')->nullable()->references('id')->on('social_secretaries')->onDelete('cascade');
            $table->string('social_secretary_number')->nullable();
            $table->string('contact_email')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('social_secretary_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_social_secretary_details');
        Schema::table('companies', function (Blueprint $table) {
            $table->string('social_secretary_number')->nullable();
        });
    }
};
