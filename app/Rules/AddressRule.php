<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AddressRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($attribute === 'address') {
            $address_rules = [
                'street_house_no' => 'required|string|max:255',
                'postal_code'     => 'required|string|max:50',
                'city'            => 'required|string|max:50',
                'country'         => 'required|string|max:50',
                'latitude'        => 'nullable|numeric',
                'longitude'       => 'nullable|numeric',
                // 'status'      => 'required|boolean',
            ];

            $validator = \Validator::make($value, $address_rules);

            if ($validator->fails()) {
                $fail($validator->errors()->first());
            }
        }
    }
}
