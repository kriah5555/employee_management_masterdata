<?php

namespace App\Http\Requests\Contract;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\Planning\PlanContractSignedRule;

class EmployeePlanSignContractRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'signature'  => ['required', 'string'],
            'company_id' => ['nullable', 'integer', Rule::exists('master.companies', 'id')],
            'plan_id'    => ['required', 'integer', new PlanContractSignedRule(request()->input('company_id'))],
        ];
    }
}
