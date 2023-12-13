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
        Schema::create('app_setting_options', function (Blueprint $table) {
            $table->id();
            $table->string('options')->nullable(false);
            $table->unsignedBigInteger('role_id');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->unsignedBigInteger('user_id');
            $table->foreignId('content_id')->nullable()->references('id')->on('app_setting_options')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('app_setting_options');
        Schema::dropIfExists('app_settings');
    }
};
