<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\EmployeeContractDetailsRule;
use App\Rules\EmployeeFunctionDetailsRule;


class EmployeeContractRequest extends ApiRequest
{

    public function rules(): array
    {
        $rules = [
            'employee_profile_id'       => new EmployeeLinkedToCompanyRule(getCompanyId()),
            'employee_contract_details' => ['bail', 'required', 'array', new EmployeeContractDetailsRule()],
            'employee_function_details' => ['bail', 'required', 'array', new EmployeeFunctionDetailsRule()],
        ];

        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($rules['employee_profile_id']);
        }

        return $rules;
    }
}
