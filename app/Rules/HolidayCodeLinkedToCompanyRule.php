<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Company\Company;

class HolidayCodeLinkedToCompanyRule implements ValidationRule
{
    protected $company_id;

    public function __construct($company_id = '')
    {
        if ($company_id) {
            $this->company_id = $company_id;
        }
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $company = Company::find($this->company_id);

        if (!$company || !$company->holidayCodes->contains($value)) {
            $fail("The selected :attribute is not linked to the company.");
        }
    }
}
