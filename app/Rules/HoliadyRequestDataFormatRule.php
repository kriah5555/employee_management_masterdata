<?php

namespace App\Rules;

use Closure;
use Illuminate\Validation\Rule;
use App\Rules\HolidayCodeLinkedToCompanyRule;
use Illuminate\Contracts\Validation\ValidationRule;
use PSpell\Config;

class HoliadyRequestDataFormatRule implements ValidationRule
{
    public function __construct(protected $durationType)
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
            $duration_type_rules = $this->getDateFormatRule($fail,$value,$data,$this->durationType);

            $validator = \Validator::make($value, $duration_type_rules);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                foreach ($errors as $error) {
                    $fail("{$attribute}.{$error}");
                }
            }
        }
    }
    public function getDateFormatRule($fail, $value, $data, $durationType)
    {
        $dateFormatRule = [];
        if ($durationType === config('absence.MULTIPLE_DATES')) {
            $dateFormatRule = [
                'from_date' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'to_date'   => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT')
            ];
        } else {
            
        }
        return $dateFormatRule;
    }

}
