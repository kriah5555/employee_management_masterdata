<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Employee\EmployeeProfile;

class EmployeeLinkedToCompanyRule implements Rule
{
    protected $company_id;

    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    public function passes($attribute, $value)
    {
        $employee = EmployeeProfile::findOrFail($value);

        return $employee->company_id == $this->company_id;
    }

    public function message()
    {
        return 'The :attribute is not linked to specified company';
    }
}
