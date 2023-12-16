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
        if (schema::hasTable('vacancies') && !Schema::hasColumns('vacancies', ['workstation_id', 'function_id']) ) {
            Schema::table('vacancies', function (Blueprint $table) {
                $table->foreignId('workstation_id')->references('id')->on('workstations');
                $table->integer('function_id');
                $table->dropColumn('start_date_time');
                $table->dropColumn('end_date_time');
                $table->date('start_date');
                $table->date('end_date')->nullable();
                $table->time('start_time');
                $table->time('end_time');
                $table->integer('repeat_type');
                $table->integer('created_by')->nullable()->change();
                $table->integer('updated_by')->nullable()->change();
            });
        }
        if (schema::hasTable('vacancy_post_employee') && !Schema::hasColumns('vacancy_post_employee', ['vacancy_date']) ){
            Schema::table('vacancy_post_employee', function (Blueprint $table) {
                $table->date('vacancy_date');
                $table->integer('responded_by')->nullable()->change();
                $table->timestamp('responded_at')->nullable()->change();
                $table->integer('plan_id')->nullable()->change();
            });
        }
        if (schema::hasTable('vacancy_employee_types')){
            Schema::table('vacancy_employee_types', function (Blueprint $table) {
                $table->integer('status')->default(1)->nullable()->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vacancies', function (Blueprint $table) {
            $table->dropColumn(['workstation_id', 'function_id']);
        });
    }
};
