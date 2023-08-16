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
        # drop the old columns
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['street', 'postal_code', 'city', 'country']);
        });

        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('address')->nullable()->references('id')->on('address')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // If you need to rollback the migration, you can re-add the dropped columns here
        Schema::table('companies', function (Blueprint $table) {
            $table->string('street')->nullable();
            $table->integer('postal_code')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();

            // Drop the newly added column
            $table->dropForeign(['address']);
            $table->dropColumn('address');
        });
    }
};
