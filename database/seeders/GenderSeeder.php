<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee\Gender;

class GenderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $genders = [
            ['name' => 'Male', 'sort_order' => 1],
            ['name' => 'Female', 'sort_order' => 2],
            ['name' => 'Non-Binary', 'sort_order' => 3],
            ['name' => 'Transgender', 'sort_order' => 4],
            ['name' => 'Genderqueer', 'sort_order' => 5],
            ['name' => 'Genderfluid', 'sort_order' => 6],
            ['name' => 'Agender', 'sort_order' => 7],
            ['name' => 'Bigender', 'sort_order' => 8],
            ['name' => 'Two-Spirit', 'sort_order' => 9],
            ['name' => 'Other', 'sort_order' => 10],
            ['name' => 'Prefer not to say', 'sort_order' => 11]
        ];
        Gender::insert($genders);
    }
}
