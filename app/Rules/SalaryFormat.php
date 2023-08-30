<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SalaryFormat implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!(preg_match('/^\d+(\.\d{1,4})?$/', $value) || preg_match('/^\d+(\,\d{1,4})?$/', $value))) {
            $fail('Incorrect salary format.');
        }
    }
}