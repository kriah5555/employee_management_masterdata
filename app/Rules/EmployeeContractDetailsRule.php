<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\EmployeeType\EmployeeTypeService;

class EmployeeContractDetailsRule implements ValidationRule
{
    protected $employeeTypeService;
    public function __construct()
    {
        $this->employeeTypeService = app(EmployeeTypeService::class);;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!array_key_exists('employee_type_id', $value) || $employee_category_id == '') {
            $fail('Please select employee type');
        } else {
            $employeeType = $this->employeeTypeService->getEmployeeTypeDetails($employee_category_id);
            $employee_category_id = $employeeType->employeeTypeCategory->id;

            if ($employeeType->employeeTypeCategory->sub_category_types) {
                if (!array_key_exists('sub_type', $value)) {
                    $fail('Please select sub type');
                } elseif (!in_array($value['sub_type'], array_keys(config('constants.SUB_TYPE_OPTIONS')))) {
                    $fail('Incorrect sub type');
                }
            }

            if ($employeeType->employeeTypeCategory->schedule_types) {
                if (!array_key_exists('schedule_type', $value)) {
                    $fail('Please select schedule type');
                } elseif (!in_array($value['schedule_type'], array_keys(config('constants.SCHEDULE_TYPE_OPTIONS')))) {
                    $fail('Incorrect schedule type');
                }
            }

            if ($employeeType->employeeTypeCategory->employment_types) {
                if (!array_key_exists('employment_type', $value)) {
                    $fail('Please select employment type');
                } elseif (!in_array($value['employment_type'], array_keys(config('constants.EMPLOYMENT_TYPE_OPTIONS')))) {
                    $fail('Incorrect employment type');
                }
            }
            
            if ($employee_category_id == config('constants.LONG_TERM_CONTRACT_ID') && !array_key_exists('weekly_contract_hours', $value)) {
                if (!array_key_exists('weekly_contract_hours', $value)) {
                    $fail('Please enter weekly contract hours');
                } elseif (!is_numeric(str_replace(',', '.', $value['weekly_contract_hours']))) {
                    $fail('Incorrect weekly contract hours');
                }

                if (!array_key_exists('work_days_per_week', $value)) {
                    $fail('Please enter work days per week');
                } elseif (!is_int(is_numeric($value['weekly_contract_hours'])) || $value['weekly_contract_hours'] > 7) {
                    $fail('Incorrect work days per week');
                }
            }
        }
        
        if (!array_key_exists('start_date', $value) || strtotime($value['start_date']) === false) {
            $fail('Please select correct contract start date');
        }
    }
}
