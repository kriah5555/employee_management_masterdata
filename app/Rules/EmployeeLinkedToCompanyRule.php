<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use App\Models\Company\Employee\EmployeeProfile;

class EmployeeLinkedToCompanyRule implements Rule
{
    protected $company_id;

    public function __construct()
    {
    }

    public function passes($attribute, $value)
    {
        $employee = EmployeeProfile::findOrFail($value);

        return !empty($employee);
    }

    public function message()
    {
        return 'The :attribute is not linked to specified company';
    }
}
