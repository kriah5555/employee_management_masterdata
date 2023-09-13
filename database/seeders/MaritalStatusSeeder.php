<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee\MaritalStatus;

class MaritalStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maritalStatuses = [
            ['name' => 'Single', 'sort_order' => 1],
            ['name' => 'Married', 'sort_order' => 2],
            ['name' => 'Civil Partnership', 'sort_order' => 3],
            ['name' => 'Separated', 'sort_order' => 4],
            ['name' => 'Divorced', 'sort_order' => 5],
            ['name' => 'Widowed', 'sort_order' => 6],
            ['name' => 'In a Relationship', 'sort_order' => 7],
            ['name' => 'Other', 'sort_order' => 8]
        ];
        MaritalStatus::insert($maritalStatuses);
    }
}
