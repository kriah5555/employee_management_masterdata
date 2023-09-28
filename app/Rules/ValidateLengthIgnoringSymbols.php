<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidateLengthIgnoringSymbols implements ValidationRule
{
    protected $symbolsToIgnore;
    protected $minLength;
    protected $maxLength;

    public function __construct($minLength, $maxLength, $symbolsToIgnore = [])
    {
        $this->minLength = $minLength;
        $this->maxLength = $maxLength;
        $this->symbolsToIgnore = $symbolsToIgnore;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Remove specified symbols from the input value
        $cleanedValue = str_replace($this->symbolsToIgnore, '', $value);

        // Check if the cleaned value's length is within the specified range
        $length = strlen($cleanedValue);
        if (!is_numeric($cleanedValue) || $length < $this->minLength || $length > $this->maxLength) {
            $fail(__('Incorrect :attribute value', ['attribute' => str_replace('_', ' ', $attribute)]));
        }
    }
}