<?php

namespace App\Http\Requests\Company\Absence;

use PSpell\Config;
use App\Rules\HolidayTypeRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\AbsenceDatesValidationRule;
use App\Rules\HolidayCodeDurationTypeRule;
use App\Repositories\Planning\PlanningRepository;
use App\Repositories\Employee\EmployeeProfileRepository;
use Illuminate\Support\Facades\Validator;

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

        $rules = [
            'duration_type'       => [
                'bail',
                'required',
                Rule::in([config('absence.MULTIPLE_DATES'), config('absence.FULL_DAYS'), config('absence.SHIFT_LEAVE')]),
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
            'plan_timings' => ['bail', 'nullable', 'array'],
            'plan_ids'     => ['bail', 'nullable', 'array'],
        ];

    
        return $rules;
    }

    protected function prepareForValidation()
    {
        $route_name = $this->route()->getName();
        if ($route_name == 'add-leave' || $route_name == 'update-leave') {

            $formattedData                        = [];
            $formattedData['employee_profile_id'] = request()->input('employee_profile_id');
            $formattedData['reason']              = request()->input('reason');
            if (request()->input('duration_type') == 1) {
                $formattedData['dates']         = request()->input('dates');
                $formattedData['duration_type'] = config('absence.FULL_DAYS');
            } else {
                $formattedData['dates'] = [
                    'from_date' => request()->input('from_date'),
                    'to_date'   => request()->input('to_date')
                ];
                $formattedData['duration_type'] = config('absence.MULTIPLE_DATES');
            }
            $plan_ids = request()->input('plan_ids');
            $formattedData['plan_timings']          = !empty($plan_ids) ? $plan_ids : null;
            $formattedData['holiday_code_counts'][] = [
                'holiday_code'  => request()->input('holiday_code_id'),
                'hours'         => 0,
                'duration_type' => null
            ];

        } elseif ($route_name == 'employee-shift-leave' || $route_name == 'shift-leave') { # employee can request for shift leave only

            $plan = app(PlanningRepository::class)->getPlanningById(request()->input('plan_id'));
            if ($plan) {
                $formattedData = [
                    'employee_profile_id' => $plan->employee_profile_id,
                    'reason'              => request()->input('reason'),
                    'dates'               => [$plan->plan_date],
                    'duration_type'       => config('absence.SHIFT_LEAVE'),
                    'plan_ids'            => [request()->input('plan_id')],
                    'holiday_code_counts' => [['holiday_code' => request()->input('holiday_code_id'), 'hours' => 0, 'duration_type' => ""]],
                    'plan_timings'        => [],
                ];
            }
        }

        if ($route_name == 'add-leave' || $route_name == 'shift-leave' || $route_name == 'update-leave') { # as manager
            $user_id                     = Auth::guard('web')->user()->id;
            $employee_profile            = getEmployeeProfileByUserId($user_id);
            $formattedData['manager_id'] = $employee_profile->id;
        } else {
            $formattedData['manager_id'] = $this->input('manager_id') ?? app(EmployeeProfileRepository::class)->getEmployeeResponsiblePersonId(request()->input('employee_profile_id'));
        }

        $this->replace($formattedData);
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $route_name = $this->route()->getName();
            if ($route_name == 'employee-shift-leave' || $route_name == 'shift-leave') { 
                if (empty($this->plan_ids)) {
                    $this->validator->errors()->add('plan_ids', "Please select plan id");
                }
            }
        });
    }

    protected function setEndDate()
    {
        $startDate = date('Y-m-d', strtotime($this->input('start_date')));
        $endDate = $this->input('end_date');
        $endDate = $endDate ? date('Y-m-d', strtotime($endDate)) : date('Y-m-d', strtotime($startDate . '+1 year'));
        $this->merge([
            'start_date' => $startDate,
            'end_date'   => $endDate
        ]);
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
