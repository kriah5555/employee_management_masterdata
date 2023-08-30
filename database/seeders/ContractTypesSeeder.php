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
            ['name' => 'Daily contract', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Flexible', 'created_by' => 0, 'updated_by' => 0],
            ['name' => 'Overtime', 'created_by' => 0, 'updated_by' => 0],
        ];
        ContractType::insert($contractTypeList);
    }
}
