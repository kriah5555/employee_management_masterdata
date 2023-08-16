<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\EmployeeType\EmployeeTypeContract;
use Database\Factories\EmployeetypeFactory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class EmployeeTypeContractFactory extends Factory
{
    protected $model = EmployeeTypeContract::class;

    public function definition(): array
    {
        return [
            'employee_type_id'    => EmployeeTypeFactory::new()->create()->id,
            'contract_type_id'    => ContractTypesFactory::new()->create()->id,
            'contract_renewal_id' => ContractRenewalFactory::new()->create()->id,
        ];
    }
}
