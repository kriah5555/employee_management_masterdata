<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;

class BelgiumCurrencyFormatRule implements Rule
{

    public function passes($attribute, $value)
    {
        return $this->isEuropeanCurrencyFormat($value);
        // return $this->isEuropeanCurrencyFormat($value) || is_numeric($value);
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
