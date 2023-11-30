<?php

namespace App\Http\Requests\Company\Absence;

use App\Rules\HolidayTypeRule;
use App\Rules\DurationTypeRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\EmployeeHolidayBalanceRule;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\HoliadyRequestDataFormatRule;
use App\Rules\HolidayCodeLinkedToCompanyRule;

class HolidayRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $companyId = $this->header('Company-Id');

        return [
            'duration_type' => [
                'bail',
                'required',
                Rule::in(array_keys(config('absence.DURATION_TYPE'))),
            ],
            // 'absence_status' => [
            //     'bail',
            //     'required',
            //     Rule::in(array_keys(config('absence.STATUS'))),
            // ],
            'employee_profile_id' => [
                'bail',
                'required',
                'integer',
                // new EmployeeLinkedToCompanyRule($companyId),
            ],
            'manager_id' => [
                'bail',
                'required',
                'integer',
                // new EmployeeLinkedToCompanyRule($companyId),
            ],
            'reason' => 'required|string',
            'dates' => [
                'required',
                'array',
                'bail',
            ],

            // 'dates.*' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
            'dates' => new HoliadyRequestDataFormatRule(request()->input('duration_type')),



            'holiday_code_counts' => [
                'bail',
                'required',
                'array',
                // new HolidayTypeRule(),
                new EmployeeHolidayBalanceRule(request()->input('employee_profile_id')),
                new DurationTypeRule(request()->input('duration_type'), $this->header('Company-Id')),

            ],

            // // 'holiday_codes.*.hours' => 'required|numeric',
            // 'holiday_code_counts.*.holiday_code' => 'required|integer',

            // 'holiday_codes.*.holiday_code' => [
            //     'bail',
            //     'required',
            //     'integer',
            //     new HolidayCodeLinkedToCompanyRule($companyId),
            // ],
        ];
    }

    public function messages()
    {
        return [
            'duration_type.required' => 'Duration type name is required.',
            'duration_type.in' => 'Invalid duration type selected.',

            'absence_status.required' => 'Absence status type name is required.',
            'absence_status.in' => 'Invalid absence status type selected.',
        ];
    }
}