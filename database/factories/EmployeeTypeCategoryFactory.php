<?php

namespace Database\Factories;

use App\Models\EmployeeType\EmployeeTypeCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EmployeeTypeCategory>
 */
class EmployeeTypeCategoryFactory extends Factory
{
    protected $model = EmployeeTypeCategory::class;

    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Worker', 'Servant', 'HQ Servant']),
            'created_by' => 0,
            'updated_by' => 0,
        ];
    }
}
