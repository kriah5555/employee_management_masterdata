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
        Schema::create('dashboard_access', function (Blueprint $table) {
            $table->id();
            $table->string('unique_key');
            $table->string('type'); // 'companies' or 'locations'
            $table->timestamp('validity');
            $table->unsignedBigInteger('company_location_id');
            $table->boolean('status');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('company_location_id')
                ->references('id')
                ->on('companies') // Update with your companies table name
                ->on('locations'); // Update with your locations table name
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dashboard_access');
    }
};
