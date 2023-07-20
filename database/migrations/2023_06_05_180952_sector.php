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
        if (!Schema::hasTable('sectors')) {
            Schema::create('sectors', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->float('paritair_committee', 9, 6)->nullable(true);
                $table->longText('description')->nullable(true);
                $table->boolean('status')->default(true);
                $table->timestamps();
                $table->integer('created_by')->nullable(true);
                $table->integer('updated_by')->nullable(true);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sectors');
    }
};
