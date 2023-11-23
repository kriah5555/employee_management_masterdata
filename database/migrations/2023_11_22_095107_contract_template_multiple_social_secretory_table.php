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
        Schema::create('contract_template_social_secretary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_template_id')->nullable()->references('id')->on('contract_templates')->onDelete('cascade');
            $table->foreignId('social_secretary_id')->nullable()->references('id')->on('social_secretaries')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->dropColumn('social_secretary_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_template_social_secretary');
        Schema::table('contract_templates', function (Blueprint $table) {
            $table->foreignId('social_secretary_id')->nullable()->references('id')->on('social_secretaries')->onDelete('cascade');
        });
    }
};
