<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmployeeType\EmployeeType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeetypeFactory extends Factory
{
    protected $model = EmployeeType::class;

    public function definition()
    {   
        return [
            'name'        => $this->faker->randomElement(['Normal employee', 'Student', 'Flex', 'Ext']),
            'description' => $this->faker->sentence,
            'employee_type_categories_id' => EmployeeTypeCategoryFactory::new()->create()->id,
            'status'      => 1
        ];
    }
}
