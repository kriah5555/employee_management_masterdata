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
        Schema::create('interim_agencies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employer_id')->nullable();
            $table->string('sender_number')->nullable();
            $table->string('username')->nullable();
            $table->string('joint_commissioner_number')->nullable();
            $table->string('rsz_number')->nullable();
            $table->foreignId('address')->nullable()->references('id')->on('address')->onDelete('cascade');
            $table->string('status')->default(true);
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('interim_agencies');
    }
};
