<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;

class StorePlanningRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'location_id'              => [
                'required',
                'integer',
                Rule::exists('locations', 'id'),
            ],
            'workstation_id'           => [
                'required',
                'integer',
                Rule::exists('workstations', 'id'),
            ],
            'employee_id'              => [
                'required',
                'integer',
                Rule::exists('employee_profiles', 'id'),
            ],
            'employee_type_id'         => [
                'required',
                'integer',
                // Rule::exists('employee_types', 'id'),
            ],
            'function_id'              => [
                'required',
                'integer',
                // Rule::exists('function_titles', 'id'),
            ],
            'dates'                    => 'nullable|array',
            'dates.*'                  => 'bail|date_format:d-m-Y',
            'timings'                  => 'required|array',
            'timings.*.start_time'     => 'required|date_format:H:i',
            'timings.*.end_time'       => 'required|date_format:H:i',
            'timings.*.contract_hours' => 'required|string',
            'timings.*.plan_id'        => 'integer',
        ];

    }
    public function messages()
    {
        return [
            'name.required'      => t('Employee type name is required.'),
            'name.string'        => 'Employee type must be a string.',
            'name.max'           => 'Employee type cannot be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max'    => 'Description cannot be greater than 255 characters.',
            'status.boolean'     => 'Status must be a boolean value.',
            'contract_types.*'   => 'Invalid contract type'
        ];
    }
}
