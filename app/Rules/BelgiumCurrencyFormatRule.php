<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class BelgiumCurrencyFormatRule implements Rule
{

    public function passes($attribute, $value)
    {
        return $this->isEuropeanCurrencyFormat($value) || ctype_digit($value); # ctype_digit will convert string to number and validate it
    }

    private function isEuropeanCurrencyFormat($value): bool
    {
        return preg_match(config('regex.EUROPE_CURRENCY_FORMAT_REGEX'), $value);
    }
    
    public function message(): string
    {
        return 'Wrong currency format :attribute.';
    }
}