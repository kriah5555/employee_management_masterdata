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
        Schema::table('address', function (Blueprint $table) {
            $table->string('street_house_no')->default(''); // Merge 'street' and 'house_no' into a single 'address' field
            $table->dropColumn(['street', 'house_no']); // Remove the 'street' and 'house_no' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('address', function (Blueprint $table) {
            $table->dropColumn('street_house_no'); // Revert back by dropping the 'address' column
            $table->string('street');
            $table->string('house_no');
        });
    }
};
