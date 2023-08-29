<?php

namespace App\Http\Rules;

use App\Services\WorkstationService;

class WorkstationRequest extends ApiRequest
{
    public function rules() :array
    {
        $workstation_rules = WorkstationService::getWorkstationRules(false);
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($workstation_rules['company']);
        }
        return $workstation_rules;
    }

    public function messages()
    {
        return [
            'workstation_name.required' => 'The workstation name is required.',
            'workstation_name.string'   => 'The workstation name must be a string.',
            'workstation_name.max'      => 'The workstation name cannot exceed 255 characters.',

            'sequence_number.required' => 'The sequence number is required.',
            'sequence_number.integer'  => 'The sequence number must be an integer.',

            'status.required' => 'The status field is required.',
            'status.boolean'  => 'The status field must be a boolean.',

            'function_titles.required' => 'At least one function title is required.',
            'function_titles.array'    => 'Function titles must be provided in an array.',
            'function_titles.*.exists' => 'One or more selected function titles are invalid.',
        ];
    }
}
