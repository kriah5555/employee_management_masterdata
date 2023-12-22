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
        Schema::table('time_registration', function (Blueprint $table) {
            $table->string('status')->default(true)->change();
            $table->integer('start_reason_id')->nullable()->change();
            $table->integer('stop_reason_id')->nullable()->change();
            $table->integer('started_by')->nullable()->change();
            $table->integer('ended_by')->nullable()->change();
        });
        Schema::table('planning_base', function (Blueprint $table) {
            $table->boolean('plan_started')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('planning_base', function (Blueprint $table) {
            $table->dropColumn('plan_started');
        });
    }
};
