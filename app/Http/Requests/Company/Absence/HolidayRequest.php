<?php

namespace App\Http\Requests\Company\Absence;

use PSpell\Config;
use App\Rules\HolidayTypeRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Auth;
use App\Rules\AbsenceDatesValidationRule;
use App\Rules\EmployeeHolidayBalanceRule;
use App\Rules\EmployeeLinkedToCompanyRule;
use App\Rules\HolidayCodeDurationTypeRule;
use App\Rules\HolidayCodeLinkedToCompanyRule;
use App\Services\Company\Absence\AbsenceService;
use App\Rules\ResponsiblePersonExistsRule;
use App\Repositories\Employee\EmployeeProfileRepository;

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
            'reason' => 'nullable|string',
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
            'manager_id' => [
                'bail',
                'nullable',
                new ResponsiblePersonExistsRule(getCompanyId()),
            ],
        ];
    }

    protected function prepareForValidation()
    {
        $absenceService = app(AbsenceService::class);
        if ($this->route()->getName() == 'employee-apply-holidays-mobile' || $this->route()->getName() == 'manager-add-employee-holiday-mobile' || $this->route()->getName() == 'manager-add-employee-holiday') {
            $user_id                     = Auth::guard('web')->user()->id;
            $employee_profile            = getEmployeeProfileByUserId($user_id);
            $employee_profile_id         = $this->route()->getName() == 'manager-add-employee-holiday' || $this->route()->getName() == 'manager-add-employee-holiday-mobile' ? $this->input('employee_profile_id') : $employee_profile->id;
            $responsible_person_id       = $this->input('manager_id') ?? app(EmployeeProfileRepository::class)->getEmployeeResponsiblePersonId($employee_profile_id);
            $this->replace([
                'employee_profile_id' => $employee_profile_id,
                'manager_id'          => $responsible_person_id,
                'reason'              => $this->input('reason'),
                'dates'               => $this->input('dates'),
                'holiday_code_counts'       => $absenceService->formatHolidayCodeCountsForApplyingAbsence(
                                            $this->input('half_day'), 
                                            $this->input('multiple_holiday_codes'),
                                            $this->input('holiday_code_counts'), 
                                            $this->input('holiday_code'), 
                                            $this->input('holiday_code_first_half'), 
                                            $this->input('holiday_code_second_half')
                                        ),
                'duration_type' => $absenceService->formatDurationTypeForApplyingAbsence($this->input('half_day'), $this->input('multiple_holiday_codes')),
            ]);
        } else {
            $this->merge(['manager_id' => $this->input('manager_id') ?? app(EmployeeProfileRepository::class)->getEmployeeResponsiblePersonId($this->input('employee_profile_id'))]);
        }
    }

    public function messages()
    {
        return [
            'duration_type.required' => 'Duration type is required.',
            'duration_type.in'       => 'Invalid duration type selected.',

            'absence_status.required' => 'Absence status type name is required.',
            'absence_status.in'       => 'Invalid absence status type selected.',
        ];
    }
}
