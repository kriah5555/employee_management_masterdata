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
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('social_secretary_id')->nullable()->references('id')->on('social_secretaries')->onDelete('cascade');
            $table->foreignId('interim_agency_id')->nullable()->references('id')->on('interim_agencies')->onDelete('cascade');
            $table->string('oauth_key')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('social_secretary_id');
            $table->dropColumn('interim_agency_id');
            $table->dropColumn('oauth_key');
        });
    }
};
