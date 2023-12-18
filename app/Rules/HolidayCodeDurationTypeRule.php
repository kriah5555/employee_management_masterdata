<?php

namespace App\Rules;

use Closure;
use Illuminate\Validation\Rule;
use App\Rules\HolidayCodeLinkedToCompanyRule;
use Illuminate\Contracts\Validation\ValidationRule;

# to validate the duration types in holiday codes and validate the holiday hours are given or not
class HolidayCodeDurationTypeRule implements ValidationRule
{
    public function __construct(protected $durationType, protected $companyId)
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        foreach ($value as $index => $data) {

            $duration_type_rules = $this->getHolidayCodeDurationTypeRule($fail, $this->companyId, $value, $data);

            $validator = \Validator::make($data, $duration_type_rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                foreach ($errors as $error) {
                    $fail("{$attribute}.{$index}.{$error}");
                }
            }
        }
    }
    public function getHolidayCodeDurationTypeRule($fail, $companyId, $value, array $data)
    {

        $durationTypeRule = [
            'holiday_code'  => ['required', 'integer', new HolidayCodeLinkedToCompanyRule($companyId)],
            'duration_type' => '',
        ];

        switch ($this->durationType) {
            case config('absence.FIRST_HALF'):
                break;

            case config('absence.SECOND_HALF'):
                break;

            case config('absence.MULTIPLE_HOLIDAY_CODES'):
                $durationTypeRule['hours'] = 'required|numeric';
                break;

            case config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'): # dont add break statement it should continue to next case because both have same validation
            case config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'):

                    if (!collect($value)->contains('duration_type', '')) { # at least one holiday code should be selected for multiple holiday code
                        $fail("Please select holiday code for multiple holiday codes.");
                    }

                    if (!collect($value)->contains('duration_type', '1')) { # holiday code should be selected for First or second half holiday code
                        $duration = $this->durationType == config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF') ? 'First half' : 'Second half';
                        $fail("Holiday code for $duration is not selected.");
                    }
                    $durationTypeRule['hours'] = 'nullable|numeric|required_if:duration_type,null|required_if:duration_type,""';
                    break;

            case config('absence.FIRST_AND_SECOND_HALF'):
                if (count($value) == 2) { # it should only have first and second half holiday codes
                    if (!collect($value)->contains('duration_type', config('absence.FIRST_HALF')) || !collect($value)->contains('duration_type', config('absence.SECOND_HALF'))) {
                        $fail("Please select First and second half holiday code.");
                    }
                } else {
                    $fail("Only first and second half holiday codes should be selected.");
                }
                break;

            case config('absence.MULTIPLE_DATES'): # don't add break statement it should continue to next case because both have same validation
            case config('absence.FULL_DAYS'):
                if (count($value) != 1) {
                    $fail("Only one holiday codes should be selected");
                }
                break;
        }

        return $durationTypeRule;
    }
}
