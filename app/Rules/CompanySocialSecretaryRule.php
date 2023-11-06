<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class CompanySocialSecretaryRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($attribute === 'social_Secretary_details') {
            $address_rules = [
                'sender_number'           => 'nullable|digits_between:1,11',
                'social_secretary_number' => 'nullable|string',
                'contact_email'           => 'nullable|string',
                'social_secretary_id'     => [
                    'bail',
                    'nullable',
                    Rule::exists('social_secretaries', 'id')
                ],
            ];

            $validator = \Validator::make($value, $address_rules);

            if ($validator->fails()) {
                $fail($validator->errors()->first());
            }
        }
    }
}
