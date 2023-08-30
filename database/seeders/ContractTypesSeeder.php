<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Contract\ContractType;

class ContractTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractTypeList = [
            ['name' => 'Daily contract', 'contract_renewal_type_id' => 1, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flexible', 'contract_renewal_type_id' => 1, 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Overtime', 'contract_renewal_type_id' => 1, 'created_by' => 0, 'updated_by' => 0],
        ];
        ContractType::insert($contractTypeList);
    }
}
