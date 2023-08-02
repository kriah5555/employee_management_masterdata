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
        // print_r($value);exit;
        foreach($value as $age => $val) {
            if (!is_int($val) || $val < 0 || $val > 100)
            {
                $error = "Incorrect salary percentage given for age :age.";
                $replacements = [
                    ':age' => $age
                ];
                $error = Str::replaceArray(array_keys($replacements), array_values($replacements), $error);
                $fail($error);
            }
        }
    }
}
