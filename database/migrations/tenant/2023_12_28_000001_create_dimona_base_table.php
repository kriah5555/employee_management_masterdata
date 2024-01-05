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
        Schema::create('dimona_base', function (Blueprint $table) {
            $table->id();
            $table->uuid('unique_id')->nullable();
            $table->string('dimona_code');
            $table->string('dimona_channel')->default('rest');
            $table->string('employee_id');
            $table->string('employee_rsz');
            $table->integer('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimona_base');
    }
};
