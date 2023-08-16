<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contracts\{ContractRenewal, ContractTypes};
use App\Models\{Dimona\DimonaType, EmployeeType\EmployeeTypeCategory};

class EmployeeTypeConfigFirstSeed extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractTypeList = [
            ['name' => 'Long term Contract','contract_type_key' => 'long_term', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Day contract', 'contract_type_key' => 'day_contract', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'External','contract_type_key' => 'external', 'created_by' => 0, 'updated_by' => 0],
        ];

        $contractRenewalList = [
            ['name' => 'Day', 'duration' => 'day', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Week', 'duration' => 'week', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Month', 'duration' => 'month', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Quarter', 'duration' => 'quarter', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Year', 'duration' => 'year', 'created_by' => 0, 'updated_by' => 0],
        ];

        $dimonaType = [
            ['name' => 'Student', 'dimona_type_key' => 'student', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flex', 'dimona_type_key' => 'flexi', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'OTH', 'dimona_type_key' => 'oth', 'created_by' => 0, 'updated_by' => 0]
        ];

        $employeeTypeCategory = [
            ['name' => 'Worker', 'created_by' => 0, 'updated_by' => 0, ],
            ['name' => 'Servant','created_by' => 0, 'updated_by' => 0 ],
        ];

        ContractRenewal::insert($contractRenewalList);
        ContractTypes::insert($contractTypeList);
        DimonaType::insert($dimonaType);
        EmployeeTypeCategory::insert($employeeTypeCategory);

    }
}
