<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmployeeType\EmployeeType;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeTypeFactory extends Factory
{
    protected $model = EmployeeType::class;

    public function definition()
    {   
        $values = [
            'name'                      => $this->faker->randomElement(['Normal employee', 'Student', 'Flex', 'Ext']),
            'description'               => $this->faker->sentence,
            'employee_type_category_id' => EmployeeTypeCategoryFactory::new()->create()->id,
            'status'                    => 1
        ];

        $employee_type = EmployeeType::create($values);
        
        $employee_type->contractTypes()->sync([
            ContractTypesFactory::new()->create()->id, 
            ContractTypesFactory::new()->create()->id
        ]);

        return $values;
    }
}
