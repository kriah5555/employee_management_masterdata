<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use App\Rules\LocationLinkedToCompanyRule;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\WorkstationLinkedToCompanyRule;
use App\Rules\WorkstationLinkedToLocationRule;

class CostCenterRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9 ]+$/',
            ],
            'company_id' => [
                'required',
                Rule::exists('companies', 'id'),
            ],
            'cost_center_number' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[0-9]{6}$/',
            ],
            'location_id' => [
                'bail',
                'integer',
                'required',
                Rule::exists('locations', 'id'),
                new LocationLinkedToCompanyRule(request()->input('company_id')),
            ],
            'status'         => 'required|boolean',
            'workstations'   => 'required|array',
            'workstations.*' => [
                'bail',
                'integer',
                Rule::exists('workstations', 'id'),
                new WorkstationLinkedToCompanyRule(request()->input('company_id')),
                new WorkstationLinkedToLocationRule(request()->input('location_id')),
            ],
            'employees'   => 'bail|nullable|array',
            'employees.*' => [
                'bail',
                'integer',
                Rule::exists('employee_profiles', 'id')->where('status', 1),
                new EmployeeLinkedToCompanyRule(request()->input('company_id')),
            ],
        ];
    }

    public function messages()
    {
        return [
            'cost_center_number.string' => 'The cost center number must be a string.',
            'cost_center_number.max'    => 'The cost center number may not be greater than :max characters.',
            'cost_center_number.regex'  => 'The cost center number must consist of exactly six digits.',
        ];
    }
}
