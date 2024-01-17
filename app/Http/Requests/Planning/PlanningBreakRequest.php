<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;

class PlanningBreakRequest extends ApiRequest
{
    public function rules(): array
    {
        $routeName = $this->route()->getName();
        $rules = [
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('planning_base', 'id'),
            ],
        ];
        if ($routeName == 'start-break') {
            $rules['start_time'] = 'required|date_format:H:i';
        } elseif ($routeName == 'stop-break') {
            $rules['stop_time'] = 'required|date_format:H:i';
        }
        return $rules;
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
