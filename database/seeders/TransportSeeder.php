<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee\Transport;

class TransportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $maritalStatuses = [
            ['name' => 'Personal Vehicle', 'sort_order' => 1],
            ['name' => 'Public Transit', 'sort_order' => 2],
            ['name' => 'Bicycle', 'sort_order' => 3],
            ['name' => 'Walking', 'sort_order' => 4],
            ['name' => 'Carpool', 'sort_order' => 5],
            ['name' => 'Company Shuttle', 'sort_order' => 6],
            ['name' => 'Telecommute/Remote', 'sort_order' => 7],
            ['name' => 'Other', 'sort_order' => 8],
            ['name' => 'Not Applicable', 'sort_order' => 9]
        ];
        Transport::insert($maritalStatuses);
    }
}