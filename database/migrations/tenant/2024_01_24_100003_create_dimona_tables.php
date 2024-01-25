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
        Schema::dropIfExists('dimona_base');
        Schema::create('dimona_base', function (Blueprint $table) {
            $table->id();
            $table->string('error_code');
            $table->string('description');
            $table->timestamps();
        });
        Schema::create('dimona_error_codes', function (Blueprint $table) {
            $table->id();
            $table->string('error_code');
            $table->string('description');
            $table->timestamps();
        });
        Schema::table('dimona_errors', function (Blueprint $table) {
            $table->unsignedBigInteger('dimona_error_code_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dimona_errors');
    }
};
