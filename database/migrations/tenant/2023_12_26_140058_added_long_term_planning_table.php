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
        Schema::rename('longterm_planning', 'long_term_planning');
        Schema::rename('longterm_planning_timings', 'long_term_planning_timings');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('long_term_planning', 'longterm_planning');
        Schema::rename('long_term_planning_timings', 'longterm_planning_timings');
    }
};
