<?php

namespace App\Http\Rules;

use App\Services\WorkstationService;
use Illuminate\Validation\Rule;
use App\Rules\ExistsInMasterDatabaseRule;

class WorkstationRequest extends ApiRequest
{
    public function rules() :array
    {
        return WorkstationService::getWorkstationRules(false);
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
            'function_titles.*.exists' => 'One or more selected function titles are invalid.',
        ];
    }
}
