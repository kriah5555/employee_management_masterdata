<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;
use App\Services\Company\LocationService;

class LocationRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $location_rules = LocationService::getLocationRules();
        foreach ($value as $index => $location) {
            $validator = \Validator::make($location, $location_rules);
            
            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                foreach ($errors as $error) {
                    $fail("{$attribute}.{$index}.{$error}");
                }
            }
        }
    }
}
