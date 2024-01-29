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
        Schema::table('companies', function (Blueprint $table) {
            $table->tinyInteger('daily_registration')->default(1); // 1 => Based on social secretary, 2 => Yes, 3=> No
        });
        Schema::table('social_secretaries', function (Blueprint $table) {
            $table->boolean('daily_registration')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('daily_registration');
        });
        Schema::table('social_secretaries', function (Blueprint $table) {
            $table->dropColumn('daily_registration');
        });
    }
};
