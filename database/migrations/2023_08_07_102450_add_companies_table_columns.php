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
            $table->string('employer_id')->nullable();
            $table->string('sender_number')->nullable();
            $table->string('joint_commission_number')->nullable();
            $table->string('rsz_number')->nullable();
            $table->string('social_secretary_number')->nullable();
            $table->string('username')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('employer_id');
            $table->dropColumn('sender_number');
            $table->dropColumn('joint_commission_number');
            $table->dropColumn('rsz_number');
            $table->dropColumn('social_secretary_number');
            $table->dropColumn('username');
        });
    }
};
