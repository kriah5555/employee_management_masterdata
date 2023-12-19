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
            ],
            'employee_contract_id' => [
                'bail',
                'integer',
                'required',
            ],
            'contract_status' => 'bail|required|integer|in' . implode(',', array_keys(config('contracts.CONTRACT_STATUS')))
        ];
    }
}
