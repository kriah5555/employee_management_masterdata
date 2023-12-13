<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class FromAndToDateRule implements ValidationRule
{
    public function __construct(protected $from_date, protected $to_date)
    {

    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (strtotime($this->from_date) > strtotime($this->to_date)) {
            $fail("From date cannot be greater than to date.");
        }
    }
}
