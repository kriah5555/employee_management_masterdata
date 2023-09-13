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
            ['name' => 'Single', 'order' => 1],
            ['name' => 'Married', 'order' => 2],
            ['name' => 'Civil Partnership', 'order' => 3],
            ['name' => 'Separated', 'order' => 4],
            ['name' => 'Divorced', 'order' => 5],
            ['name' => 'Widowed', 'order' => 6],
            ['name' => 'In a Relationship', 'order' => 7],
            ['name' => 'Other', 'order' => 8]
        ];
        MaritalStatus::insert($maritalStatuses);
    }
}
