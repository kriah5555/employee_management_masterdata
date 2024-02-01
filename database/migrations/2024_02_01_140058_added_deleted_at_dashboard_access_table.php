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
        Schema::table('dashboard_access', function (Blueprint $table) {
            $table->string('type')->change(); #'company', location
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dashboard_access', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropSoftDeletes();
        });
        Schema::table('dashboard_access', function (Blueprint $table) {
            $table->tinyInteger('type'); #'company', location
        });
    }
};
