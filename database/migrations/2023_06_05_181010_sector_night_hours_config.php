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
        Schema::create('sector_night_hours_config', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key');
            $table->string('type'); // 'companies' or 'locations'
            $table->timestamp('validity');
            $table->foreignId('company_location_id')
                ->constrained(['companies', 'locations'])
                ->onDelete('cascade');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sector_night_hours_config');
    }
};
