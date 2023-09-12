<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use App\Rules\LocationLinkedToCompanyRule;

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
                'rejex:/^[a-zA-Z0-9 ]+$/',
            ],
            'company_id' => [
                'required',
                Rule::exists('companies', 'id'),
            ],
            'cost_center_number' => [
                'nullable',
                'string',
                'ma x:255',
                'regex:/^[0-9]{6}$/',
            ],
            'location_id' => [
                'required',
                Rule::exists('locations', 'id'),
                new LocationLinkedToCompanyRule(request()->input('company_id')),
            ],
            'status'         => 'required|boolean',
            'workstations'   => 'required|array',
            'workstations.*' =>  [
                'bail',
                'integer',
                Rule::exists('workstations', 'id'),
                new WorkstationLinkedToCompanyRule(request()->input('company'))
        ],
        ];
    }

    protected function passedValidation()
    {
        // Remove 'company_id' from the validated data
        $this->request->remove('company_id');
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