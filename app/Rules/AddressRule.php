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
                'street'      => 'required|string|max:255',
                'house_no'    => 'required|string|max:50',
                'postal_code' => 'required|string|max:50',
                'city'        => 'required|string|max:50',
                'country'     => 'required|string|max:50',
                'latitude'    => 'nullable|numeric',
                'longitude'   => 'nullable|numeric',
                // 'status'      => 'required|boolean',
            ];

            $address_messages = [
                'street.required'      => 'The street field is required.',
                'street.string'        => 'The street must be a string.',
                'street.max'           => 'The street may not be greater than :max characters.',
                'house_no.required'    => 'The house number field is required.',
                'house_no.string'      => 'The house number must be a string.',
                'house_no.max'         => 'The house number may not be greater than :max characters.',
                'postal_code.required' => 'The postal code field is required.',
                'postal_code.string'   => 'The postal code must be a string.',
                'postal_code.max'      => 'The postal code may not be greater than :max characters.',
                'city.required'        => 'The city field is required.',
                'city.string'          => 'The city must be a string.',
                'city.max'             => 'The city may not be greater than :max characters.',
                'country.required'     => 'The country field is required.',
                'country.string'       => 'The country must be a string.',
                'country.max'          => 'The country may not be greater than :max characters.',
                'latitude.numeric'     => 'The latitude must be a numeric value.',
                'latitude.between'     => 'The latitude must be between -90 and 90 degrees.',
                'longitude.numeric'    => 'The longitude must be a numeric value.',
                'longitude.between'    => 'The longitude must be between -180 and 180 degrees.',
            ];

            $validator = \Validator::make($value, $address_rules, $address_messages);

            if ($validator->fails()) {
                $fail($validator->errors()->first());
            }
        }
    }
}
