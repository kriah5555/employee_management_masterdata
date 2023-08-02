<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use App\Http\Rules\ApiRequest;

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
            'internal_code'                     => 'required|integer',
            'description'                       => 'nullable|string|max:255',
            'holiday_type'                      => 'required|in:1,2,3',
            'count_type'                        => 'required|in:1,2,3',
            'icon_type'                         => 'required|in:1,2,3,4',
            'consider_plan_hours_in_week_hours' => 'required|in:0,1',
            'employee_category'                 => 'required|in:1,2,3',
            'contract_type'                     => 'required|in:1,2,3',
            'carry_forword'                     => 'required|in:0,1',
            'status'                            => 'required|boolean',
            'created_by'                        => 'nullable|integer',
            'updated_by'                        => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'holiday_code_name.required' => 'The holiday code name field is required.',
            'holiday_code_name.string'   => 'The holiday code name must be a string.',
            'holiday_code_name.max'      => 'The holiday code name must not exceed 255 characters.',

            'internal_code.required' => 'The internal code field is required.',
            'internal_code.integer'  => 'The internal code must be an integer.',

            'description.string' => 'The description must be a string.',
            'description.max'    => 'The description must not exceed 255 characters.',

            'holiday_type.required' => 'The holiday type field is required.',
            'holiday_type.in'       => 'Invalid holiday type selected.',

            'count_type.required' => 'The count type field is required.',
            'count_type.in'       => 'Invalid count type selected.',

            'icon_type.required' => 'The icon type field is required.',
            'icon_type.in'       => 'Invalid icon type selected.',

            'consider_plan_hours_in_week_hours.required' => 'The consider plan hours in week hours field is required.',
            'consider_plan_hours_in_week_hours.in'       => 'Invalid value for consider plan hours in week hours.',

            'employee_category.required' => 'The employee category field is required.',
            'employee_category.in'       => 'Invalid employee category selected.',

            'contract_type.required' => 'The contract type field is required.',
            'contract_type.in'       => 'Invalid contract type selected.',

            'carry_forword.required' => 'The carry forward field is required.',
            'carry_forword.in'       => 'Invalid value for carry forward.',

            'created_by.integer' => 'The created by field must be an integer.',
            'updated_by.integer' => 'The updated by field must be an integer.',

            'status.boolean' => 'Status must be a boolean value.',
        ];
    }
}
