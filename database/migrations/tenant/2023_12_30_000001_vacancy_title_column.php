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
        if (Schema::hasTable('vacancies') && !Schema::hasColumn('vacancies', 'name')) {
            Schema::table('vacancies', function (Blueprint $table) {
                $table->string('name')->default('title');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('vacancies') && Schema::hasColumn('vacancies', 'name')) {
            Schema::table('vacancies', function (Blueprint $table) {
                $table->dropColumn(['name']);
            });
        }
    }
};
