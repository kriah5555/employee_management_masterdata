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
        ];
    }
}
