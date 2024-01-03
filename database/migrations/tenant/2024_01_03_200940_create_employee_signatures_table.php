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
        Schema::create('employee_signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_profile_id')->nullable()->references('id')->on('employee_profiles')->onDelete('cascade');
            $table->longText('signature_data')->nullable(); # base 64 encoded
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_signatures', function (Blueprint $table) {
            $table->dropForeign(['employee_profile_id']);
        });

        Schema::dropIfExists('employee_signatures');
    }
};
