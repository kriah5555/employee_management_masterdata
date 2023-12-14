<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\EmployeeContractDetailsRule;
use App\Rules\EmployeeFunctionDetailsRule;


class EmployeeContractRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'employee_contract_details' => ['bail', 'required', 'array', new EmployeeContractDetailsRule()],
            'employee_function_details' => ['bail', 'required', 'array', new EmployeeFunctionDetailsRule()],
        ];
    }
}
