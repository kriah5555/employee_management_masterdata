<?php

namespace App\Http\Requests\Company;

use Illuminate\Validation\Rule;
use App\Rules\LocationLinkedToCompanyRule;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\WorkstationLinkedToCompanyRule;
use App\Rules\WorkstationLinkedToLocationRule;
use App\Models\Company\CostCenter;
use App\Rules\UniqueCostCenterNumberInCompanyRule;
use App\Http\Requests\ApiRequest;

class CostCenterRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules =  [
            'name' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9 ]+$/',
            ],
            'company_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('companies', 'id'),
            ],
            'cost_center_number' => [
                'required',
                'string',
                'max:255',
                'regex:/^[0-9]{6}$/',
                new UniqueCostCenterNumberInCompanyRule($this->route('cost_center')),           
            ],
            'location_id' => [
                'bail',
                'integer',
                'required',
                Rule::exists('locations', 'id'),
                new LocationLinkedToCompanyRule(),
            ],
            'status'         => 'required|boolean',
            'workstations'   => 'required|array',
            'workstations.*' => [
                'bail',
                'integer',
                Rule::exists('workstations', 'id'),
                new WorkstationLinkedToCompanyRule(),
                new WorkstationLinkedToLocationRule(request()->input('location_id')),
            ],
            'employees'   => 'bail|nullable|array',
            'employees.*' => [
                'bail',
                'integer',
                Rule::exists('employee_profiles', 'id')->where('status', 1),
                new EmployeeLinkedToCompanyRule(request()->header('Company-Id')),
            ],
        ];

        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['cost_center_id'] = [
                'bail',
                'integer',
                'required',
                Rule::exists('cost_centers', 'id'),
            ];
        }
        return $rules;
    }

    public function prepareForValidation()
    {
        // Check if the request method is "PUT" or "PATCH" (update request)
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            // Access the cost_center_id from the request data
            if ($this->input('cost_center_id')) {
                $costCenterId = $this->input('cost_center_id');
                // Retrieve the associated CostCenter model
                $costCenter = CostCenter::findOrFail($costCenterId);

                // Set the company_id in the request data based on the cost_center's company_id
                $this->merge(['company_id' => $costCenter->location->company]);
            }
        }
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
