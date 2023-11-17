<?php

namespace App\Http\Requests\Company\Contract;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\LocationLinkedToCompanyRule;
use App\Rules\EmployeeTypeLinkedToCompanyRule;
use App\Http\Requests\ApiRequest;

class ContractConfigurationRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locations' => 'required|array',
            'locations.*.location_id' => [
                'required',
                'integer',
                new LocationLinkedToCompanyRule()
            ],
            'locations.*.employee_type_contract_status' => 'required|array',

            'locations.*.employee_type_contract_status.*.employee_type_id' => [
                'required',
                'integer',
                new EmployeeTypeLinkedToCompanyRule(request()->header('Company-Id'))
            ],

            'locations.*.employee_type_contract_status.*.status' => 'required|boolean',
        ];
    }
}
