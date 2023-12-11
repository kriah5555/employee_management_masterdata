<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Holiday\HolidayCode;
use Illuminate\Validation\Rule;

class HolidayCountFieldRule implements ValidationRule
{
    protected $holiday_code_id;

    public function __construct($holiday_code_id = '')
    {
        if ($holiday_code_id) {
            $this->holiday_code_id = $holiday_code_id;
        }
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rules = [
            'numeric',
            'regex:/^\d+(\.\d{1,2})?$/',
        ];

        foreach ($rules as $rule) {
            $validator = validator([$attribute => $value], [$attribute => $rule]);
            if ($validator->fails()) {
                $fail($validator->errors()->first($attribute));
                return;
            }
        }

        // Check if the count exceeds the count in the HolidayCodes table
        if ($this->holiday_code_id) {
            $holidayCode = HolidayCode::findOrFail($this->holiday_code_id);
            if ($holidayCode->count_type == config('absence.DAYS')) {
                // If count_type is 2, modify the count value
                $count = $holidayCode->count / config('constants.DAY_HOURS');
            } else {
                $count = $holidayCode->count;
            }
            $count_type = strtolower(config('absence.HOLIDAY_COUNT_TYPE_OPTIONS')[$holidayCode->count_type]);
            if ($holidayCode && $value > $count) {
                $fail("The :attribute cannot exceed the maximum count of {$count} {$count_type}");
            }
        }
    }
}
