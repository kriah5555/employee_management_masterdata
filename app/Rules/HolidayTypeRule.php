<?php

namespace App\Rules;

use Closure;
use App\Models\Holiday\HolidayCode;
use Illuminate\Contracts\Validation\ValidationRule;

// will check if t holiday codes
class HolidayTypeRule implements ValidationRule
{
    public function __construct(protected $holiday_code_id)
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $holidayCodes = request()->input('holiday_codes');

        // Extract the holiday_code_ids from the input array
        $holidayCodeIds = array_column($this->holiday_code_id, 'holiday_code_id');

        // Query the database once to check all holiday codes at once
        $invalidHolidayCodes = HolidayCode::whereIn('id', $holidayCodeIds)
            ->where('holiday_type', '!=', config('constants.HOLIDAY'))
            ->pluck('id')
            ->toArray();

        if (!empty($invalidHolidayCodes)) {
            $fail("The holiday code types are not valid for the following IDs: " . implode(', ', $invalidHolidayCodes));
        }
    }
}