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
            'plan_switch_id' => [
                'required',
                'integer',
                Rule::exists('tenant.employee_profiles', 'id')->where('deleted_at', null),
            ],
            'status' => 'required|in:' . config('constants.SWITCH_PLAN_APPROVE') . ',' . config('constants.SWITCH_PLAN_REJECT')
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

    protected function validatePlanForSwitch()
    {
        $plan_switch_id = $this->input('plan_switch_id');
        $status         = $this->input('status');

        $employee_switch_plan_data = EmployeeSwitchPlanning::where('status', true)->find($plan_switch_id);
        if ($status == config('constants.SWITCH_PLAN_APPROVE')) {
            if (empty($employee_switch_plan_data)) {
                $this->validator->errors()->add('plan_switch_id', "The plan is not longer available to switch.");
            } else {
    
                $planDetails = $employee_switch_plan_data->plan;
        
                if (strtotime($planDetails->start_date_time) <= strtotime(now())) {
                    $this->validator->errors()->add('plan_id', "Cannot update status, plan end time has exceeded.");
                }
        
                if (count($planDetails->timeRegistrations)) {
                    $this->validator->errors()->add('plan_id', "Cannot switch plan which is already started");
                }
            }
        }
    }
}
