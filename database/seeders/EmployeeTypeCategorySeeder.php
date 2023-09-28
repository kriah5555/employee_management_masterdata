<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\EmployeeType\EmployeeTypeCategory;

class EmployeeTypeCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $employeeTypeCategory = [
            ['name' => 'Long term Contract', 'sub_category_types' => 1, 'schedule_types' => 1, 'employement_types' => 1, 'created_by' => 0, 'updated_by' => 0, 'sort_order' => 1],
            ['name' => 'Day Contract', 'sub_category_types' => 0, 'schedule_types' => 0, 'employement_types' => 0, 'created_by' => 0, 'updated_by' => 0, 'sort_order' => 2],
            ['name' => 'External', 'sub_category_types' => 0, 'schedule_types' => 0, 'employement_types' => 0, 'created_by' => 0, 'updated_by' => 0, 'sort_order' => 3],
        ];
        EmployeeTypeCategory::insert($employeeTypeCategory);
    }
}