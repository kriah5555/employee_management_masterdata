<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use Illuminate\Support\Facades\Auth;
use App\Services\Planning\PlanningService;
use Illuminate\Foundation\Http\FormRequest;
use App\Models\Planning\EmployeeSwitchPlanning;
use App\Services\Employee\EmployeeContractService;

class EmployeeSwitchPlanningRequest extends ApiRequest
{
    public function rules()
    {
        return [
            'plan_id' => [
                'required',
                'integer',
                Rule::exists('planning_base', 'id')->where('deleted_at', null),
            ],
            'request_from' => [
                'required',
                'integer',
                Rule::exists('employee_profiles', 'id')->where('deleted_at', null),
            ],
            'request_to' => [
                'required',
                'integer',
                Rule::exists('employee_profiles', 'id')->where('deleted_at', null),
            ],
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
            $this->validateIfEmployeeHasActiveContract();
            $this->validateIfPlanAlreadyRequested();
        });
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

    protected function validateIfEmployeeHasActiveContract()
    {
        $planId = $this->input('plan_id');

        if (!empty($planId)) {
            $plan = app(PlanningService::class)->getPlanningById($this->input('plan_id'));

            $employee_contracts = app(EmployeeContractService::class)->getEmployeeWithActiveType(date('Y-m-d', strtotime($plan->start_date_time)), $plan->employee_type_id);
            if ($employee_contracts->isEmpty()) {
                $this->validator->errors()->add('request_to', "Selected employee does not have active contract");
            }
        }
    }

    protected function validateIfPlanAlreadyRequested()
    {
        $planId = $this->input('plan_id');

        if (!empty($planId)) {
            if (EmployeeSwitchPlanning::where(['plan_id' => $planId, 'request_to' => $this->input('request_to')])->get()->isNotEmpty()) {
                $this->validator->errors()->add('request_to', "Already Requested to selected employee");
            }
        }
    }
}
