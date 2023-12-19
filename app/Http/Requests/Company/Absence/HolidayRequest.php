<?php

namespace App\Http\Requests\Company\Absence;

use PSpell\Config;
use App\Rules\HolidayTypeRule;
use App\Rules\HolidayCodeDurationTypeRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\AbsenceDatesValidationRule;
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
        $companyId = getCompanyId();

        $absence_id = request()->route('holiday');
        return [
            'duration_type' => [
                'bail',
                'required',
                Rule::in(array_keys(config('absence.DURATION_TYPE'))),
            ],
            'employee_profile_id' => [
                'bail',
                'required',
                'integer',
                new EmployeeLinkedToCompanyRule()
            ],
            'manager_id' => [
                'bail',
                'required',
                'integer',
            ],
            'reason' => 'required|string',

            'dates' => [
                'bail',
                'required',
                'array',
                new AbsenceDatesValidationRule(request()->input('employee_profile_id'), request()->input('duration_type'), $absence_id)
            ],
            'dates.*' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
            'holiday_code_counts' => [
                'bail',
                'required',
                'array',
                new HolidayCodeDurationTypeRule(request()->input('duration_type'), $companyId, $absence_id),
                new EmployeeHolidayBalanceRule(request()->input('employee_profile_id'), request()->input('duration_type'), $absence_id)
            ],
        ];
    }

    public function messages()
    {
        return [
            'duration_type.required' => 'Duration type is required.',
            'duration_type.in' => 'Invalid duration type selected.',

            'absence_status.required' => 'Absence status type name is required.',
            'absence_status.in' => 'Invalid absence status type selected.',
        ];
    }
}
