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
            $table->string('dimona_type');
            $table->string('dimona_code');
            $table->string('company_vat');
            $table->string('company_rsz');
            $table->string('company_smals_id');
            $table->string('dimona_channel')->default('rest');
            $table->string('employee_id');
            $table->string('employee_name');
            $table->string('employee_rsz');
            $table->timestamp('start_date_time')->nullable();
            $table->timestamp('end_date_time')->nullable();
            $table->integer('send_status')->default(1);
            $table->integer('response_status')->default(0);
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
