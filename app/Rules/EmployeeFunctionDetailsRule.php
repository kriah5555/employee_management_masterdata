<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\EmployeeFunction\FunctionService;

class EmployeeFunctionDetailsRule implements ValidationRule
{
    protected $employeeTypeService;

    protected $functionService;
    public function __construct(EmployeeTypeService $employeeTypeService, FunctionService $functionService)
    {
        $this->employeeTypeService = $employeeTypeService;
        $this->functionService = $functionService;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $employeeContractDetails = request()->input('employee_contract_details');
        if (is_array($employeeContractDetails) && isset($employeeContractDetails['employee_type_id'])) {
            $usedFunction = [];
            $employeeType = $this->employeeTypeService->getEmployeeTypeDetails($employeeContractDetails['employee_type_id']);
            foreach ($value as $data) {
                if (!array_key_exists('function_id', $data)) {
                    $fail('Please select function');
                } else {
                    $usedFunction[] = $functionTitle = $this->functionService->getFunctionTitleDetails($data['function_id']);
                }
                print_r($functionTitle->functionCategory);
                exit;
            }
            // Now you can use $employeeTypeId
            // ...
        }
        // print_r('here');
        // exit;
    }
}