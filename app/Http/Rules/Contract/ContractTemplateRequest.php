<?php

namespace App\Http\Rules\Contract;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\SectorLinkedToEmployeeTypeRule;
use App\Rules\CompanyLinkedToSocialSecretaryRule;
use App\Rules\ContractTemplateUniqueCombinationRule;
use App\Rules\ExistsInMasterDatabaseRule;
use App\Models\EmployeeType\EmployeeType;
use App\Models\SocialSecretary\SocialSecretary;
use App\Models\Company\Company;

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
                new ExistsInMasterDatabaseRule('employee_types'),
            ],
            'social_secretary_id' => [
                'bail',
                'nullable',
                'integer',
                new ExistsInMasterDatabaseRule('social_secretaries'),
            ],
            'company_id' => [
                'bail',
                'nullable',
                'integer',
                new ExistsInMasterDatabaseRule('companies'),
                new CompanyLinkedToSocialSecretaryRule($this->input('social_secretary_id')),
            ],
            'pdf_file' => '',
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