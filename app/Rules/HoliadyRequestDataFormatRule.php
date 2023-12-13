<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HoliadyRequestDataFormatRule implements ValidationRule
{
    public function __construct(protected $durationType)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $duration_type_rules = $this->getDateFormatRule($fail, $value, $this->durationType);


        $validator = \Validator::make($value, $duration_type_rules);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            foreach ($errors as $error) {
                $fail("{$attribute}.{$error}");
            }
        }
    }

    public function getDateFormatRule($fail, $value, $durationType)
    {
        $dateFormatRule = [];

        if ($durationType === config('absence.MULTIPLE_DATES')) {
            $dateFormatRule = [
                'from_date' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'to_date' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT')
            ];
        } else {
            $dateFormatRule = [
                '*' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT')
            ];
        }
        return $dateFormatRule;
    }
}
