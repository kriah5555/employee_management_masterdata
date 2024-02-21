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
        Schema::table('planning_break', function (Blueprint $table) {
            $table->timestamp('break_start_time')->nullable()->change();
            $table->timestamp('break_end_time')->nullable()->change();
            $table->integer('started_by')->nullable()->change();
            $table->boolean('status')->nullable()->change();
            $table->integer('ended_by')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planning_break', function (Blueprint $table) {
            $table->timestamp('break_start_time')->nullable(false)->change();
            $table->timestamp('break_end_time')->nullable(false)->change();
            $table->integer('started_by')->nullable(false)->change();
            $table->boolean('status')->nullable(false)->change();
            $table->integer('ended_by')->nullable(false)->change();
        });
    }
};
