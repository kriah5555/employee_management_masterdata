<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('sector_salary_steps', function (Blueprint $table) {
            $table->renameColumn('step_number', 'level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('sector_salary_steps', function (Blueprint $table) {
            $table->renameColumn('level', 'step_number');
        });
    }
};
