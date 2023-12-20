<?php

namespace App\Http\Requests\Contract;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Http\Requests\ApiRequest;

class ContractRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            // 'employee_profile_id' => [
            //     'bail',
            //     'required',
            //     'integer',
            //     new EmployeeLinkedToCompanyRule()
            // ],
            'employee_contract_id' => [
                'bail',
                'integer',
                'required',
            ],
        ];
    }
}
