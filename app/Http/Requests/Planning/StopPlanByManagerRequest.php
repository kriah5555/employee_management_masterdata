<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\BelgiumCurrencyFormatRule;
use App\Rules\Planning\PlanStopRule;

class StopPlanByManagerRequest extends ApiRequest
{
    public function rules(): array
    {
        return [
            'plan_id'   => [
                'required',
                'integer',
                Rule::exists('planning_base', 'id'),
                new PlanStopRule
            ],
            'reason_id' => [
                'nullable',
                'integer',
                // Rule::exists('employee_types', 'id'),
            ],
            'reason'    => 'required_if:reason_id,null:string',
            'stop_time' => 'required|date_format:H:i',
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
