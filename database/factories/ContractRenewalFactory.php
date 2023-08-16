<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contracts\ContractRenewal;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ContractRenewalFactory extends Factory
{
    protected $model = ContractRenewal::class;
    
    public function definition()
    {
        return [
            'name' => $this->faker->randomElement(['Day', 'Week', 'Month', 'Quarter', 'Year']), 
            'duration' => $this->faker->randomElement(['day', 'week', 'month', 'quarter', 'year']), 
            'created_by' => 0, 
            'updated_by' => 0
        ];
    }
}
