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
            foreach ($value as $index => $val) {
                $percentage       = $val['percentage'];
                $max_time_to_work = isset($val['max_time_to_work']) ? $val['max_time_to_work'] : ''; // Access the max_time_to_work field
        
                if (!is_int((int) $percentage) || $percentage < 0 || $percentage > 100) {
                    $error = t("Incorrect :attribute.$index salary percentage.");
                    $fail($error);
                }
        
                if (empty($max_time_to_work) || !preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $max_time_to_work)) {
                    $error = t("Incorrect :attribute.$index time format. It should be in 24-hour time format (HH:MM).");
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