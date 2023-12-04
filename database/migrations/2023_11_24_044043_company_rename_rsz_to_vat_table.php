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
        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('employer_id', 'vat_number');
            $table->renameColumn('address', 'address_id');
            $table->renameColumn('logo', 'logo_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->renameColumn('vat_number', 'employer_id');
            $table->renameColumn('address_id', 'address');
            $table->renameColumn('logo_id', 'logo');
        });
    }
};
