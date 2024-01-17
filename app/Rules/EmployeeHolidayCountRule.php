<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Rules\HolidayCountFieldRule;
use App\Rules\HolidayCodeLinkedToCompanyRule;
use Illuminate\Validation\Rule;

class EmployeeHolidayCountRule implements ValidationRule
{
    protected $company_id;

    public function __construct($company_id)
    {
        if ($company_id) {
            $this->company_id = $company_id;
        }
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $holidayCodeCounts = collect($value);

        // Validate each item in the "holiday_code_counts" array
        $holidayCodeCounts->each(function ($item, $index) use ($fail) {
            $rules = [
                "holiday_code_counts.$index.holiday_code_id" => [
                    'bail',
                    'required',
                    'integer',
                    new HolidayCodeLinkedToCompanyRule($this->company_id),
                ],
                "holiday_code_counts.$index.count" => [
                    'bail',
                    'required',
                ],
                "holiday_code_counts.$index.reason" => [
                    'nullable',
                ],
            ];

            // Check if "holiday_code_id" exists in $item before adding the validation rule
            if (isset($item['holiday_code_id'])) {
                $rules["holiday_code_counts.$index.count"][] = new HolidayCountFieldRule($item['holiday_code_id']);
            }

            $validator = validator(request()->only(array_keys($rules)), $rules);

            if ($validator->fails()) {
                $fail($validator->errors()->first());
            }
        });
    }
}
