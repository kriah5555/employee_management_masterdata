<?php

namespace App\Http\Rules\HolidayCode;

use App\Http\Rules\ApiRequest;
use App\Rules\HolidayCountFieldRule;

class HolidayCodeRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {
        return [
            'holiday_code_name'                 => 'required|string|max:255',
            // 'count'                             => 'bail|required|numeric|regex:/^\d+(\.\d{1,2})?$/',
            'internal_code'                     => 'required|integer',
            'description'                       => 'nullable|string|max:255',
            'holiday_type'                      => 'required|in:1,2,3',
            'count_type'                        => 'required|in:1,2,3',
            'icon_type'                         => 'required|in:1,2,3,4',
            'consider_plan_hours_in_week_hours' => 'required|in:0,1',
            'employee_category'                 => 'required|array', // Updated to support an array
            'employee_category.*'               => 'in:1,2', // Individual category values must be valid
            'contract_type'                     => 'required|in:1,2,3',
            'status'                            => 'required|boolean',
            'count'                             => [
                'bail',
                new HolidayCountFieldRule()
            ],
        ];
    }

    public function messages()
    {
        return [
            'holiday_type.required'                      => 'The holiday type field is required.',
            'holiday_type.in'                            => 'Invalid holiday type selected.',

            'count_type.required'                        => 'The count type field is required.',
            'count_type.in'                              => 'Invalid count type selected.',

            'icon_type.required'                         => 'The icon type field is required.',
            'icon_type.in'                               => 'Invalid icon type selected.',

            'consider_plan_hours_in_week_hours.required' => 'The consider plan hours in week hours field is required.',
            'consider_plan_hours_in_week_hours.in'       => 'Invalid value for consider plan hours in week hours.',

            'employee_category.required'                 => 'The employee category field is required.',
            'employee_category.in'                       => 'Invalid employee category selected.',

            'contract_type.required'                     => 'The contract type field is required.',
            'contract_type.in'                           => 'Invalid contract type selected.',
            
            'created_by.integer'                         => 'The created by field must be an integer.',
            'updated_by.integer'                         => 'The updated by field must be an integer.',

            'status.boolean'                             => 'Status must be a boolean value.',

            'count.required'                             => 'The count field is required.',
            'count.numeric'                              => 'The count field must be a numeric value.',
            'count.regex'                                => 'The count field must be a numeric value with up to two decimal places.',
        ];
    }
}
