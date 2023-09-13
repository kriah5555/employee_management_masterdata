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
            ['name' => 'Male', 'order' => 1],
            ['name' => 'Female', 'order' => 2],
            ['name' => 'Non-Binary', 'order' => 3],
            ['name' => 'Transgender', 'order' => 4],
            ['name' => 'Genderqueer', 'order' => 5],
            ['name' => 'Genderfluid', 'order' => 6],
            ['name' => 'Agender', 'order' => 7],
            ['name' => 'Bigender', 'order' => 8],
            ['name' => 'Two-Spirit', 'order' => 9],
            ['name' => 'Other', 'order' => 10],
            ['name' => 'Prefer not to say', 'order' => 11]
        ];
        Gender::insert($genders);
    }
}
