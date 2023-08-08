<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private $tables_to_add_soft_delete = [
        'sector_night_hours_config',
        'sector_age_salary',
        'employee_types',
        'sector_to_employee_types',
        'companies',
        'files',
        'holiday_codes',
        'holiday_code_count',
        'locations',
        'address',
        'workstations',
        'locations_to_workstations',
        'workstation_to_funcitons',
    ];
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sector_to_company', function (Blueprint $table) {
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);        
        });

        Schema::table('locations_to_workstations', function (Blueprint $table) {
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);        
        });

        Schema::table('workstation_to_funcitons', function (Blueprint $table) {
            $table->integer('created_by')->nullable(true);
            $table->integer('updated_by')->nullable(true);        
        });

        foreach ($this->tables_to_add_soft_delete as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->softDeletes();
            });    
        } 
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sector_to_company', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('locations_to_workstations', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });

        Schema::table('workstation_to_funcitons', function (Blueprint $table) {
            $table->dropColumn(['created_by', 'updated_by']);
        });

        foreach ($this->tables_to_add_soft_delete as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropSoftDeletes();
            });    
        }
    }
};
