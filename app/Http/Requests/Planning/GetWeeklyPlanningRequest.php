<?php

namespace App\Http\Requests\Planning;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class GetWeeklyPlanningRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'location'         => [
                'required',
                'integer',
                Rule::exists('locations', 'id'),
            ],
            'workstations'     => 'nullable|array',
            'workstations.*'   => [
                'bail',
                'integer',
                Rule::exists('workstations', 'id'),
            ],
            'employee_types'   => 'nullable|array',
            'employee_types.*' => [
                'bail',
                'integer',
                Rule::exists('master.employee_types', 'id'),
            ],
            'week'             => 'required|integer',
            'year'             => 'required|digits:4',
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