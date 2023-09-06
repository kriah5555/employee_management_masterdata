<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class SectorAgeSalaryRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($value as $val) {
            $age_data[$val['age']] = $val['percentage'];
        }
        $age = array_keys($age_data);


        if (!$this->validateAgeValues($age)) {
            $error = "Incorrect salary percentage.";
            $fail($error);
        } else {
            foreach ($age_data as $val) {
                if (!is_int((int) $val) || $val < 0 || $val > 100) {
                    $error = "Incorrect salary percentage.";
                    $fail($error);
                }
            }
        }
    }

    public function validateAgeValues(array $age)
    {
        $range = range(min($age), max($age));
        foreach ($range as $number) {
            if (!in_array($number, $age)) {
                return false;
            }
        }
        return true;
    }
}