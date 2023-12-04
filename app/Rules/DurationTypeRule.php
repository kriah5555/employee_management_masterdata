<?php

namespace App\Rules;

use Closure;
use Illuminate\Validation\Rule;
use App\Rules\HolidayCodeLinkedToCompanyRule;
use Illuminate\Contracts\Validation\ValidationRule;

class DurationTypeRule implements ValidationRule
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

        foreach ($value as $data) {

            $duration_type_rules = $this->getDurationTypeRule($fail, $this->companyId, $value, $data);

            $validator = \Validator::make($data, $duration_type_rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                foreach ($errors as $error) {
                    $fail("{$attribute}.{$error}");
                }
            }
        }
    }
    public function getDurationTypeRule($fail, $companyId, $value, array $data)
    {

        $durationTypeRule = [
            'holiday_code' => ['required', 'integer', new HolidayCodeLinkedToCompanyRule($companyId)],
        ];

        switch ($this->durationType) {
            case config('absence.FIRST_HALF'):
            case config('absence.SECOND_HALF'):
                $durationTypeRule['duration_type'] = ['required', 'integer', 'in:' . ($this->durationType === config('absence.FIRST_HALF') ? config('absence.FIRST_HALF') : config('absence.SECOND_HALF'))];
                break;
            case config('absence.MULTIPLE_HOLIDAY_CODES'):
                $durationTypeRule['hours'] = 'required|numeric';
                break;

            case config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'):
            case config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'):

                    $durationTypeRule['hours'] = 'nullable|numeric';

                    if ($data['duration_type']!="") {
                        $durationTypeRule['duration_type'] = ['required', 'integer', 'in:' . ($this->durationType === 4 ? config('absence.FIRST_HALF') : config('absence.SECOND_HALF'))];
                    }
                break;
            case config('absence.FIRST_AND_SECOND_HALF'):
                if (count($value) == 2) {
                    $durationTypes = array_column($value, 'duration_type');
                    if (count($durationTypes) === count(array_unique($durationTypes))) {
                        $durationTypeRule['duration_type'] =
                            [
                                'required',
                                'integer',
                                Rule::in(config('absence.FIRST_HALF'), config('absence.SECOND_HALF')),
                            ];
                    } else {
                        $fail("Duration type is same, Please check once");
                    }
                } else {
                    $fail("Please select two holiday code based on shift");
                }
                break;
            case config('absence.MULTIPLE_DATES'):
            case config('absence.FULL_DAYS'):
                break;
        }
        return $durationTypeRule;
    }
}
