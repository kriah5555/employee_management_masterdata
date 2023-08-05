<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Rules\AddressRule;

class CompanyCreationLocationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        foreach ($value as $index => $location) {
            $locationRules = [
                'location_name' => 'required|string|max:255',
                'status' => 'required|boolean',
                'address' => ['required', new AddressRule()],
            ];

            $validator = \Validator::make($location, $locationRules);

            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                foreach ($errors as $error) {
                    $fail("{$attribute}.{$index}.{$error}");
                }
            }
        }
    }
}
