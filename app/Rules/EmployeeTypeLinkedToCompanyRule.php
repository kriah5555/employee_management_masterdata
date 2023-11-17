<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\EmployeeType\EmployeeType;

class EmployeeTypeLinkedToCompanyRule implements ValidationRule
{
    public function __construct(protected $company_id)
    {

    }
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $employee_type = EmployeeType::find($value);
        
        if (!$this->isEmployeeTypeLinkedToCompany($employee_type)) {
            $fail(":attribute is not linked with the company");
        }
    }

    protected function isEmployeeTypeLinkedToCompany(EmployeeType $employee_type): bool
    {
        return $employee_type->sectors
            ->flatMap(fn($sector) => $sector->companies->pluck('id'))
            ->contains($this->company_id);
    }
}
