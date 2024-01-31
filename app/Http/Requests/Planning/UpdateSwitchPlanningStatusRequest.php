<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\Planning\PlanningService;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Planning\EmployeeSwitchPlanning;
use App\Services\Employee\EmployeeContractService;

class UpdateSwitchPlanningStatusRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('tenant.planning_base', 'id')->where('deleted_at', null),
            ],
            'employee_switch_plan_id' => [
                'required',
                'integer',
                Rule::exists('tenant.employee_profiles', 'id')->where('deleted_at', null),
            ],
            'status' => 'required|'
        ];
    }

    protected function prepareForValidation()
    {       
        if ($this->input('company_id')) {
            setTenantDBByCompanyId($this->input('company_id'));
        }
        $this->merge(['request_from' => getEmployeeProfileByUserId(Auth::guard('web')->user()->id)->id]);
    }

    public function withValidator($validator)
    {
        if ($this->input('company_id')) {
            setTenantDBByCompanyId($this->input('company_id'));
        }
        $validator->after(function ($validator) {
            $this->validatePlanForSwitch();
        });
    }

    protected function validatePlanIsAvailableToSwitch()
    {
        $employee_switch_plan_id = $this->input('employee_switch_plan_id');

        $employee_switch_plan_data = EmployeeSwitchPlanning::where('status', true)->find($employee_switch_plan_id);
        if (!empty($employee_switch_plan_data)) {
            $this->validator->errors()->add('employee_switch_plan_id', "The ");
        } else {

        }
    }

    protected function validatePlanForSwitch()
    {
        $planId = $this->input('plan_id');

        if (!empty($planId)) {
            $planDetails = app(PlanningService::class)->getPlanningById($planId);

            if (strtotime($planDetails->start_date_time) <= strtotime(now())) {
                $this->validator->errors()->add('plan_id', "Cannot switch plan after plan start time is crossed");
            }

            if (count($planDetails->timeRegistrations)) {
                $this->validator->errors()->add('plan_id', "Cannot switch plan which is already started");
            }
        }
    }
}
