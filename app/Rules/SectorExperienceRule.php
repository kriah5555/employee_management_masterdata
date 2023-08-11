<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SectorExperienceRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
	    foreach($value as $data) {
            if (!array_key_exists('level', $data) || !is_int((int)$data['level']))
            {
                $fail('Incorrect :attribute.');
            }
            if (!array_key_exists('from', $data) || !is_int((int)$data['from']))
            {
                $fail('Incorrect :attribute.');
            }
            if (!array_key_exists('to', $data) || !is_int((int)$data['to']))
            {
                $fail('Incorrect :attribute.');
            }
        }
    }
}
