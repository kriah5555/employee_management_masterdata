<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contract\ContractRenewalType;

class ContractRenewalTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contractRenewalTypes = [
            ['key' => 'once', 'name' => 'Once', 'sort_order' => 1],
            ['key' => 'daily', 'name' => 'Daily', 'sort_order' => 2],
            ['key' => 'weekly', 'name' => 'Weekly', 'sort_order' => 3],
            ['key' => 'monthly', 'name' => 'Monthly', 'sort_order' => 4],
            ['key' => 'quarterly', 'name' => 'Quarterly', 'sort_order' => 5],
            ['key' => 'semiannually', 'name' => 'Semi-Annually', 'sort_order' => 6],
            ['key' => 'annually', 'name' => 'Annually', 'sort_order' => 7],
            ['key' => 'manual', 'name' => 'Manual', 'sort_order' => 8],
        ];
        ContractRenewalType::insert($contractRenewalTypes);
    }
}
