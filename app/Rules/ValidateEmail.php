<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateEmail implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!validateCompanyRszNumber($value)) {
            $fail(__('Incorrect :attribute value', ['attribute' => str_replace('_', ' ', $attribute)]));
        }
    }
}
