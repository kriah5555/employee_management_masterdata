<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rule;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'name'          => 1.1,
                'description'   => 0,
                'type'          => 1,
                'default_value' => 0
            ],
        ];
        Rule::insert($rules);
    }
}