<?php

namespace App\Http\Requests\Company\Absence;

use PSpell\Config;
use App\Rules\HolidayTypeRule;
use App\Rules\HolidayCodeDurationTypeRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\AbsenceDatesValidationRule;

class LeaveRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $companyId = getCompanyId();
        $route = request()->url();
        $absence_id = $this->isMethod('PUT') ? substr($route, strrpos($route, '/') + 1) : null;
        $rules = [];
        if ($this->route()->getName() == 'add-leave') {
            $rules['duration_type'] = 'required|integer|in:1,2';
            $durationType = $this->input('duration_type');
            if ($durationType == 1) {
                $rules['dates'] = [
                    'required',
                    'array',
                ];
                $rules['dates.*'] = 'required|date_format:d-m-Y';
            } elseif ($durationType == 2) {
                $rules['from_date'] = 'required|date_format:d-m-Y';
                $rules['to_date'] = 'required|date_format:d-m-Y';
            }
            $rules['employee_profile_id'] = [
                'bail',
                'required',
                // Rule::in('tenant.employee_profiles', 'id'),
            ];
            $rules['reason'] = 'nullable|string';
            $rules['holiday_code_id'] = [
                'bail',
                'required',
            ];
            $rules['pid'] = 'nullable|string';
        } else {
            $rules = [
                'duration_type'       => [
                    'bail',
                    'required',
                    Rule::in([config('absence.PLANNING_LEAVES')]),
                ],
                'employee_profile_id' => [
                    'bail',
                    'required',
                    'integer',
                ],
                'reason'              => 'nullable|string',
                'dates'               => [
                    'bail',
                    'required',
                    'array',
                    new AbsenceDatesValidationRule(request()->input('employee_profile_id'), request()->input('duration_type'), $absence_id)
                ],
                'dates.*'             => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
                'holiday_code_counts' => [
                    'bail',
                    'required',
                    'array',
                    new HolidayCodeDurationTypeRule(request()->input('duration_type'), $companyId, $absence_id),
                ],
                'plan_timings'        => ['bail', 'nullable', 'array']
            ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'duration_type.required'  => 'Duration type is required.',
            'duration_type.in'        => 'Invalid duration type selected.',

            'absence_status.required' => 'Absence status type name is required.',
            'absence_status.in'       => 'Invalid absence status type selected.',
        ];
    }
}
