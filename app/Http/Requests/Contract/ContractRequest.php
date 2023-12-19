<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\EmployeeLinkedToCompanyRule;

class ContractRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'employee_profile_id' => [
                'bail',
                'required',
                'integer',
                new EmployeeLinkedToCompanyRule()
            ]
        ];
    }
}
