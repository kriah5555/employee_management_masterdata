<?php

namespace App\Http\Requests\Planning;

use App\Services\Planning\PlanningService;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;

class SwitchPlanningRequest extends ApiRequest
{
    public function __construct(protected PlanningService $planningService)
    {
    }
    public function rules(): array
    {
        if ($this->method() == 'GET') {
            $rules = [
                'plan_id' => [
                    'required',
                    'integer',
                    Rule::exists('planning_base', 'id'),
                ],
            ];
        } elseif ($this->method() != 'PUT') {
            $this->validator->errors()->add('start_date', "Dates overlapping with other long term plannings");
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'plan_id.required' => t('Plan id is required.'),
            'plan_id.integer'  => 'Plan id must be a integer.',
        ];
    }
    public function withValidator($validator)
    {
        // Additional custom validation logic
        $validator->after(function ($validator) {
            $this->validatePlanForSwitch();
        });
    }
    protected function validatePlanForSwitch()
    {
        $planId = $this->input('plan_id');
        $planDetails = $this->planningService->getPlanningById($planId);
        if (strtotime($planDetails->start_date_time) <= strtotime(date('Y-m-d H:i'))) {
            $this->validator->errors()->add('plan_id', "Cannot switch plan after plan start time is crossed");
        }
        if (count($planDetails->timeRegistrations)) {
            $this->validator->errors()->add('plan_id', "Cannot switch plan which is already started");
        }
    }
}
