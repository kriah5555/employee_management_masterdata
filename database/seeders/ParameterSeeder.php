<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Parameter\Parameter;

class ParameterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rules = [
            [
                'name'        => 'PAR_1A',
                'description' => "Minimum break between consecutive plans for employee",
                'type'        => 1,
                'value_type'  => 1,
                'value'       => '11'
            ],
            [
                'name'        => 'PAR_1B',
                'description' => 'Minimum break between consecutive plans for employee(below 18)',
                'type'        => 1,
                'value_type'  => 1,
                'value'       => '12'
            ],


            [
                'name'        => 'PAR_2A',
                'description' => "Maximum hours an employee is allowed to work per day",
                'type'        => 2,
                'value_type'  => 1,
                'value'       => '9'
            ],
            [
                'name'        => 'PAR_2B',
                'description' => "Minimum hours an employee is allowed to work per day",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '3'
            ],
            [
                'name'        => 'PAR_2C',
                'description' => "Maximum hours an employee is allowed to work per week",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '40'
            ],
            [
                'name'        => 'PAR_2D',
                'description' => "Minimum hours an employee is allowed to work per week",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '13'
            ],
            [
                'name'        => 'PAR_2E',
                'description' => "Maximum hours an employee(below 18) is allowed to work per day",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '8'
            ],
            [
                'name'        => 'PAR_2F',
                'description' => "Minimum hours an employee(below 18) is allowed to work per day",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '3'
            ],
            [
                'name'        => 'PAR_2G',
                'description' => "Maximum hours an employee(below 18) is allowed to work per week",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '40'
            ],
            [
                'name'        => 'PAR_2H',
                'description' => "Maximum overtime a partime employee is allowed to work per month",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '12'
            ],
            [
                'name'        => 'PAR_2I',
                'description' => "Maximum overtime a fulltime employee is allowed to work per year",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '360'
            ],
            [
                'name'        => 'PAR_2J',
                'description' => "Maximum overtime a fulltime employee is allowed to work in current month + last 3 months",
                'type'        => 2,
                'value_type'  => 2,
                'value'       => '143'
            ],
            [
                'name'        => 'PAR_3A',
                'description' => "Break for employee - If plan times is above 6.5 hours",
                'type'        => 3,
                'value_type'  => 2,
                'value'       => '30'
            ],

            [
                'name'        => 'PAR_3B',
                'description' => "Minimum break for employee (below 18) - If worked times is above 6 hours",
                'type'        => 3,
                'value_type'  => 2,
                'value'       => '60'
            ],
            [
                'name'        => 'PAR_3C',
                'description' => "Minimum break for employee (below 18) - If worked time is above 4.5 hours",
                'type'        => 3,
                'value_type'  => 2,
                'value'       => '30'
            ],
            [
                'name'        => 'PAR_4A',
                'description' => "Sometimes - for any reason - a dimona IN is pending. After how many minutes employee can STOP working (even knowing dimona UPDATE will fail)",
                'type'        => 4,
                'value_type'  => 2,
                'value'       => '180'
            ],


            [
                'name'        => 'PAR_5A',
                'description' => "How long, before the planned start time, an employee is allowed to already enter start working",
                'type'        => 5,
                'value_type'  => 2,
                'value'       => '14'
            ],
            [
                'name'        => 'PAR_5B',
                'description' => "When an employee did NOT enter start working on time : after how  many minutes (after the planned start time) do you (as manager) want to receive an alert (email) that employee did not enter start working?",
                'type'        => 5,
                'value_type'  => 2,
                'value'       => '59'
            ],
        ];
        Parameter::insert($rules);
    }
}
