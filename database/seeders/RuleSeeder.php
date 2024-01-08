<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rule\Rule;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'name'        => 'RULE_1A',
                'description' => "Minimum break between consecutive plans for employee",
                'type'        => 1,
                'value_type'  => 1,
                'value'       => '11'
            ],
            [
                'name'        => 'RULE_1B',
                'description' => 'Minimum break between consecutive plans for employee(below 18)',
                'type'        => 1,
                'value_type'  => 1,
                'value'       => '12'
            ],


            [
                'name'        => 'RULE_2A',
                'description' => "Maximum hours an employee is allowed to work per day",
                'type'        => 2,
                'value_type'  => 1,
                'value'       => '9'
            ],
            [
                'name'        => 'RULE_2B',
                'description' => "Minimum hours an employee is allowed to work per day",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '3'
            ],
            [
                'name'        => 'RULE_2C',
                'description' => "Maximum hours an employee is allowed to work per week",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '40'
            ],
            [
                'name'        => 'RULE_2D',
                'description' => "Minimum hours an employee is allowed to work per week",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '13'
            ],
            [
                'name'        => 'RULE_2E',
                'description' => "Maximum hours an employee(below 18) is allowed to work per day",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '8'
            ],
            [
                'name'        => 'RULE_2F',
                'description' => "Minimum hours an employee(below 18) is allowed to work per day",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '3'
            ],
            [
                'name'        => 'RULE_2G',
                'description' => "Maximum hours an employee(below 18) is allowed to work per week",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '40'
            ],
            [
                'name'        => 'RULE_2H',
                'description' => "Maximum overtime a partime employee is allowed to work per month",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '12'
            ],
            [
                'name'        => 'RULE_2I',
                'description' => "Maximum overtime a fulltime employee is allowed to work per year",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '360'
            ],
            [
                'name'        => 'RULE_2J',
                'description' => "Maximum overtime a fulltime employee is allowed to work in current month + last 3 months",
                'type'        => 2,
                'value_type'  => 1,
                'value'       => '143'
            ],
            [
                'name'        => 'RULE_3A',
                'description' => "Break for employee - If plan times is above 6.5 hours",
                'type'        => 3,
                'value_type'  => 1,
                'value'       => '30'
            ],
            [
                'name'        => 'RULE_3B',
                'description' => "Sometimes - for any reason - a dimona IN is pending. After how many minutes employee can STOP working (even knowing dimona UPDATE will fail)",
                'type'        => 3,
                'value_type'  => 1,
                'value'       => '180'
            ],
            [
                'name'        => 'RULE_3C',
                'description' => "Sometimes - for any reason - a dimona IN is pending. After how many minutes employee can STOP working (even knowing dimona UPDATE will fail)",
                'type'        => 3,
                'value_type'  => 1,
                'value'       => '180'
            ],



            [
                'name'        => 'RULE_3A',
                'description' => "When an employee did NOT enter start working on time : after how  many minutes (after the planned start time) do you (as manager) want to receive an alert (email) that employee did not enter start working?",
                'type'        => 3,
                'value_type'  => 1,
                'value'       => '5'
            ],
            [
                'name'        => 'RULE_3B',
                'description' => "Sometimes - for any reason - a dimona IN is pending. After how many minutes employee can STOP working (even knowing dimona UPDATE will fail)",
                'type'        => 3,
                'value_type'  => 1,
                'value'       => '180'
            ],
            [
                'name'        => 'RULE_3A',
                'description' => "How long, before the planned start time, an employee is allowed to already enter start working",
                'type'        => 3,
                'value_type'  => 3,
                'value'       => '24'
            ],
            [
                'name'        => 'RULE_3D',
                'description' => "How many minutes to the past from current time, Manager can fill start work time for employee and start the work",
                'type'        => 3,
                'value_type'  => 3,
                'value'       => '24'
            ],
            [
                'name'        => 'RULE_2I',
                'description' => "Minimum break for employee - If plan times is above 6 hours",
                'type'        => 2,
                'value_type'  => 1,
                'value'       => '30'
            ],
            [
                'name'        => 'RULE_2J',
                'description' => "Minimum break for employee (below 18) - If plan times is above 6 hours",
                'type'        => 2,
                'value_type'  => 1,
                'value'       => '30'
            ],
            [
                'name'        => 'RULE_2K',
                'description' => "Minimum break for employee (below 18) - If plan times is above 4.5 hours",
                'type'        => 2,
                'value_type'  => 1,
                'value'       => '30'
            ],
        ];
        Rule::insert($rules);
    }
}
