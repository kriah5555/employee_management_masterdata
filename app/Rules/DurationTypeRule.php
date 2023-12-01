<?php

namespace App\Rules;

use Closure;
use Illuminate\Validation\Rule;
use App\Rules\HolidayCodeLinkedToCompanyRule;
use Illuminate\Contracts\Validation\ValidationRule;

class DurationTypeRule implements ValidationRule
{
    public function __construct(protected $durationType,protected $companyId)
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

            $duration_type_rules = $this->getDurationTypeRule($fail,$this->companyId,$value,$data);

            $validator = \Validator::make($data, $duration_type_rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                foreach ($errors as $error) {
                    $fail("{$attribute}.{$error}");
                }
            }
        }
    }
    public function getDurationTypeRule($fail,$companyId,$value,array $data)
    {

        $durationTypeRule = [];
        switch ($this->durationType) {
            case 1:
                $durationTypeRule = [
                    'holiday_code' => ['required','integer',new HolidayCodeLinkedToCompanyRule($companyId)],
                    'duration_type' => ['required', 'integer', 'in:' . config('absence.FIRST_HALF')]
                ];
                break;
            case 2:
                $durationTypeRule = [
                    'holiday_code' => ['required','integer',new HolidayCodeLinkedToCompanyRule($companyId)],
                    'duration_type' => ['required', 'integer', 'in:' . config('absence.SECOND_HALF')]
                ];
                break;
            case 3:
                $durationTypeRule = [
                    'holiday_code' => ['required','integer',new HolidayCodeLinkedToCompanyRule($companyId)],
                    'hours' => 'required|numeric',
                ];
                break;
            case 4:
                $durationTypeCount = count(array_filter($value, function ($row) {
                    return isset($row['duration_type']);
                }));

                if ($durationTypeCount === 1) {
                    $durationTypeRule = [
                        'holiday_code' => ['required','integer',new HolidayCodeLinkedToCompanyRule($companyId)],
                        'hours' => 'nullable|numeric',
                    ];

                    if(isset($data['duration_type'])){

                        $durationTypeRule['duration_type'] = ['required', 'integer', 'in:' . config('absence.FIRST_HALF')];
                    }
                }
                else {
                    $fail("Plaese select one first half");
                }
                break;
            case 5:
                $durationTypeCount = count(array_filter($value, function ($row) {
                    return isset($row['duration_type']);
                }));

                if ($durationTypeCount === 1) {
                    $durationTypeRule = [
                        'holiday_code' => ['required','integer',new HolidayCodeLinkedToCompanyRule($companyId)],
                        'hours' => 'nullable|numeric',
                    ];

                    if(isset($data['duration_type']))
                    {
                        $durationTypeRule['duration_type'] = ['required', 'integer', 'in:' . config('absence.SECOND_HALF'),
                    ];
                    }
                }
                else {
                    $fail("Please select one second half");
                }
                break;

            case 6:
                if (count($value) == 2) {
                    $durationTypes = array_column($value, 'duration_type');
                    if(count($durationTypes) === count(array_unique($durationTypes))){
                        $durationTypeRule = [
                            'holiday_code' => ['required','integer',new HolidayCodeLinkedToCompanyRule($companyId)],
                            'duration_type' => [
                                'required',
                                'integer',
                                Rule::in(config('absence.FIRST_HALF'), config('absence.SECOND_HALF')),
                            ]
                        ];
                    }else{
                        $fail("Duration type is same, Please check once");
                    }
                }
                else{
                    $fail("Please select two holiday code based on shift");
                }
                break;
            case 7:
            case 8:
                $durationTypeRule = [
                    'holiday_code' => ['required','integer',new HolidayCodeLinkedToCompanyRule($companyId)],
                ];
                break;
        }
        return $durationTypeRule;
    }
}
