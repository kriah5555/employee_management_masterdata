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
            ['key' => 'daily', 'name' => 'Daily'],
            ['key' => 'weekly', 'name' => 'Weekly'],
            ['key' => 'monthly', 'name' => 'Monthly'],
            ['key' => 'quarterly', 'name' => 'Quarterly'],
            ['key' => 'semiannually', 'name' => 'Semi-Annually'],
            ['key' => 'annually', 'name' => 'Annually'],
            ['key' => 'manual', 'name' => 'Manual renewal'],
        ];
        ContractRenewalType::insert($contractRenewalTypes);
    }
}
