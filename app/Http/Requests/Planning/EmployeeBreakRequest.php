<?php

namespace App\Http\Requests\Planning;

use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;
use App\Rules\Planning\QRcodeRule;
use Illuminate\Support\Facades\Auth;
use App\Rules\Planning\StartBreakQRExistRule;
use App\Rules\Planning\StopBreakQRExistRule;
use App\Repositories\Planning\PlanningRepository;
use App\Repositories\Employee\EmployeeProfileRepository;


class EmployeeBreakRequest extends ApiRequest
{
    public function rules(): array
    {
        
        $rules = [
            'QR_code'    => ['bail', 'required', 'string', new QRcodeRule, $this->route()->getName() == 'start-break-by-employee' ? new StartBreakQRExistRule(Auth::id()) : new StopBreakQRExistRule(Auth::id())],
        ];

        return $rules;
    }

    public function messages()
    {
        return [
        ];
    }

    protected function passedValidation()
    {
        $qr_data = decodeData(request()->input('QR_code'));

        setTenantDBByCompanyId($qr_data['company_id']);

        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId(Auth::id());

        $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);
        
        $this->merge([
            'pid' => $plans->first() ? $plans->first()->id : null,
            $this->route()->getName() == 'start-break-by-employee' ? 'started_by' : 'ended_by' => Auth::id(),
            $this->route()->getName() == 'start-break-by-employee' ? 'start_time' : 'end_time' => date('H:i')
        ]);
    }
}
