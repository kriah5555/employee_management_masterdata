<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\EmployeeTypeCategorySeeder;
use Database\Seeders\DimonaTypesSeeder;
use Database\Seeders\ContractRenewalTypesSeeder;
use Database\Seeders\ContractTypesSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(EmployeeTypeCategorySeeder::class);
        $this->call(DimonaTypesSeeder::class);
        $this->call(ContractRenewalTypesSeeder::class);
        // $this->call(ContractTypesSeeder::class);
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}