<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\EmployeeType\EmployeeTypeService;

class EmployeeContractDetailsRule implements ValidationRule
{
    protected $employeeTypeService;
    public function __construct(EmployeeTypeService $employeeTypeService)
    {
        $this->employeeTypeService = $employeeTypeService;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!array_key_exists('employee_type_id', $value) || $value['employee_type_id'] == '') {
            $fail('Please select employee type');
        } else {
            $employeeType = $this->employeeTypeService->getEmployeeTypeDetails($value['employee_type_id']);
            if ($employeeType->employeeTypeCategory->sub_category_types && !array_key_exists('sub_type', $value)) {
                $fail('Please select sub type');
            } elseif (!in_array($value['sub_type'], array_keys(config('constants.SUB_TYPE_OPTIONS')))) {
                $fail('Incorrect sub type');
            }
            if ($employeeType->employeeTypeCategory->schedule_types && !array_key_exists('schedule_type', $value)) {
                $fail('Please select schedule type');
            } elseif (!in_array($value['schedule_type'], array_keys(config('constants.SCHEDULE_TYPE_OPTIONS')))) {
                $fail('Incorrect schedule type');
            }
            if ($employeeType->employeeTypeCategory->employment_types && !array_key_exists('employement_type', $value)) {
                $fail('Please select employement type');
            } elseif (!in_array($value['employement_type'], array_keys(config('constants.EMPLOYMENT_TYPE_OPTIONS')))) {
                $fail('Incorrect employement type');
            }
            if ($employeeType->id == 1 && !array_key_exists('weekly_contract_hours', $value)) {
                $fail('Please enter weekly contract hours');
            } elseif (!is_numeric(str_replace(',', '.', $value['weekly_contract_hours']))) {
                $fail('Incorrect weekly contract hours');
            }
        }
        if (!array_key_exists('start_date', $value) || strtotime($value['start_date']) === false) {
            $fail('Please select correct contract start date');
        }
        if (array_key_exists('end_date', $value) && strtotime($value['end_date']) === false) {
            $fail('Please select correct contract end date');
        }
    }
}
