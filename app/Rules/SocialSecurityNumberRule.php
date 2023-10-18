<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Repositories\EmployeeProfileRepository;

class SocialSecurityNumberRule implements ValidationRule
{
    protected $employeeProfileRepository;

    public function __construct(EmployeeProfileRepository $employeeProfileRepository)
    {
        $this->employeeProfileRepository = $employeeProfileRepository;
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $companyId = request()->route('company_id');
        if ($this->employeeProfileRepository->checkEmployeeExistsInCompany($companyId, $value)) {
            $fail("Employee already present in company");
        }
        print_r('as');
        exit;
    }
}