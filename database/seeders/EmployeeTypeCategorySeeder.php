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
            ['name' => 'Long term Contract', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Day contract', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'External', 'created_by' => 0, 'updated_by' => 0],
        ];
        EmployeeTypeCategory::insert($employeeTypeCategory);
    }
}
