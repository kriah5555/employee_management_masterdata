<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EmployeeTypeCategorySeeder;
use Database\Seeders\DimonaTypesSeeder;
use Database\Seeders\ContractRenewalTypesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            EmployeeTypeCategorySeeder::class,
            DimonaTypesSeeder::class,
            ContractRenewalTypesSeeder::class,
        ]);
    }
}