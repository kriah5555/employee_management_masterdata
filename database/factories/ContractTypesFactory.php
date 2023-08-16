<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contracts\ContractTypes;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ContractTypesFactory extends Factory
{
    protected $model = ContractTypes::class;
    public function definition()
    {
        return [
            'name' => 'Long term Contract',
            'contract_type_key' => 'long_term', 
            'created_by' => 0, 
            'updated_by' => 0
        ];
    }
}
