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
    public function __construct()
    {
        $this->employeeTypeService = app(EmployeeTypeService::class);
        $this->functionService     = app(FunctionService::class);
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $employeeContractDetails = request()->input('employee_contract');
        if (is_array($employeeContractDetails) && isset($employeeContractDetails['employee_type_id'])) {
            $usedFunction = [];
            foreach ($value as $data) {
                if (!array_key_exists('function_id', $data)) {
                    $fail('Please select function');
                } elseif (in_array($data['function_id'], $usedFunction)) {
                    $fail('Cannot link same function twice');
                } else {
                    $functionTitle = $this->functionService->getFunctionTitleDetails($data['function_id']);
                    $usedFunction[] = $functionTitle->id;
                }
                if (!array_key_exists('salary', $data) || !is_numeric(str_replace(',', '.', $data['salary']))) {
                    $fail('Please enter correct salary');
                }
                if (array_key_exists('experience', $data) && !is_numeric(str_replace(',', '.', $data['experience']))) {
                    $fail('Please enter correct experience');
                }
            }
        }
    }
}
