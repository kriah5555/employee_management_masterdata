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
        if (!Schema::hasTable('dimona_details')) {
            Schema::create('dimona_details', function (Blueprint $table) {
                $table->id();
                $table->foreignId('dimona_base_id')->references('id')->on('dimona_base')->onDelete('cascade');
                $table->string('dimona_type');
                $table->integer('status')->default(1);
                $table->timestamp('start_date_time')->nullable();
                $table->timestamp('end_date_time')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
