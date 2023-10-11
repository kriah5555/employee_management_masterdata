<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Company;

class CompanyLinkedToSocialSecretaryRule implements ValidationRule
{
    public function __construct(protected $social_secretary_id)
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value)) {
            $exists = Company::where('id', $value)
                ->where('social_secretary_id', $this->social_secretary_id)
                ->exists();

            if (!$exists) {
                $fail("The :attribute is not linked with the selected social secretary.");
            }
        }
    }
}
