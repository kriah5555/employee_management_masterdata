<?php

namespace App\Http\Requests\Absence;

use App\Rules\HolidayTypeRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\EmployeeHolidayBalanceRule;
use App\Rules\EmployeeLinkedToCompanyRule;
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
                Rule::in(array_keys(config('constants.ABSENCE_DURATION_TYPE'))),
            ],
            'absence_status' => [
                'bail',
                'required',
                Rule::in(array_keys(config('constants.ABSENCE_STATUS'))),
            ],
            'employee_profile_id' => [
                'bail',
                'required',
                'integer',
                new EmployeeLinkedToCompanyRule($companyId),
            ],
            'manager_id' => [
                'bail',
                'required',
                'integer',
                new EmployeeLinkedToCompanyRule($companyId),
            ],
            'reason' => 'required|string',
            'dates' => [
                'required',
                'array',
                'bail',
            ],

            'dates.*' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),

            'holiday_codes' => [
                'bail',
                'required',
                'array',
                // new HolidayTypeRule(),
                new EmployeeHolidayBalanceRule(request()->input('employee_profile_id')),
            ],

            'holiday_codes.*.hours' => 'required|numeric',
            'holiday_codes.*.holiday_code_id' => [
                'bail',
                'required',
                'integer',
                new HolidayCodeLinkedToCompanyRule($companyId),
            ],
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