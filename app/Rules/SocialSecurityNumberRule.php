<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\Employee\EmployeeService;

class SocialSecurityNumberRule implements ValidationRule
{
    protected $employeeService;

    public function __construct()
    {
        $this->employeeService = app(EmployeeService::class);
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $companyId = getCompanyId();
        if ($this->employeeService->checkEmployeeExistsInCompany($companyId, $value)) {
            $fail("Employee already present in company");
        }
    }
}
