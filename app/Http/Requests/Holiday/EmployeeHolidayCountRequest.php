<?php

namespace App\Http\Requests\Holiday;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;
use App\Rules\HolidayCountFieldRule;
use App\Rules\HolidayCodeLinkedToCompanyRule;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\EmployeeHolidayCountRule;

class EmployeeHolidayCountRequest extends ApiRequest
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
            'employee_id' => [
                'bail',
                'required',
                'integer',
                new EmployeeLinkedToCompanyRule(request()->header('Company-Id')),
            ],
            "holiday_code_counts" => [
                'bail',
                'array',
                'nullable',
                new EmployeeHolidayCountRule(request()->header('Company-Id')),
            ],

            // "holiday_code_counts.*.holiday_code_id" => [
            //     'bail',
            //     'required',
            //     'integer',
            //     new HolidayCodeLinkedToCompanyRule(request()->input('company_id')),
            // ],
            // "holiday_code_counts.*.count" => [
            //     'bail',
            //     'required',
            //     new HolidayCountFieldRule(request()->input('holiday_code_counts.*.holiday_code_id'))
            // ],
        ];
    }

    public function messages()
    {
        return [
            'count.required' => 'The count field is required.',
            'count.numeric'  => 'The count field must be a numeric value.',
            'count.regex'    => 'The count field must be a numeric value with up to two decimal places.',
        ];
    }
}
