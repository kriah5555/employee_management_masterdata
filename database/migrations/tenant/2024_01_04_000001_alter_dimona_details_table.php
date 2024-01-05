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
        if (Schema::hasTable('dimona_details') && !Schema::hasColumn('dimona_details', 'hours')) {
            Schema::table('dimona_details', function (Blueprint $table) {
                $table->decimal('hours')->nullable();
            });
        }

        if (Schema::hasTable('planning_dimonas') && !Schema::hasColumn('planning_dimonas', 'time_registration_id')) {
            Schema::table('planning_dimonas', function (Blueprint $table) {
                $table->foreignId('time_registration_id')->nullable()->references('id')->on('time_registration')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
