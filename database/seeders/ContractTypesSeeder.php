<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contracts\ContractTypes;

class ContractTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractTypeList = [
            ['name' => 'Daily contract', 'contract_type_key' => 'day', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flexible','contract_type_key' => 'flexible', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Overtime','contract_type_key' => 'overtime', 'created_by' => 0, 'updated_by' => 0],
        ];
        ContractTypes::insert($contractTypeList);
    }
}
