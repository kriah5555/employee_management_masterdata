<?php

namespace App\Rules\Planning;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class QRcodeRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $qr_data = decodeData($value);

        if (!isset($qr_data['company_id']) || !isset($qr_data['location_id'])) {
            $fail('Invalid Qr code data');
        }
    }
}
