<?php

namespace App\Http\Rules\Contract;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\SectorLinkedToEmployeeTypeRule;
use App\Rules\CompanyLinkedToSocialSecretaryRule;
use App\Rules\ContractTemplateUniqueCombinationRule;

class ContractTemplateRequest extends ApiRequest
{
    public function __construct()
    {
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'body'     => 'required|string',
            'status'   => 'required|boolean',
            'employee_type_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('employee_types', 'id'),
            ],
            'social_secretary_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('social_secretaries', 'id'),
            ],
            'sector_id' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('sectors', 'id'),
                new SectorLinkedToEmployeeTypeRule($this->input('employee_type_id')),
            ],
            'company_id' => [
                'bail',
                'nullable',
                'integer',
                Rule::exists('companies', 'id'),
                new CompanyLinkedToSocialSecretaryRule($this->input('social_secretary_id')),
            ],
            'language' => [
                'required',
                Rule::in(config('app.available_locales')),
                new ContractTemplateUniqueCombinationRule($this->route('contract_template')),
            ],
        ];

    }
    public function messages()
    {
        return [];
    }
}