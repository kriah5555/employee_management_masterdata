<?php

namespace App\Rules\Planning;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\Planning\PlanningStartStopService;

use App\Repositories\Planning\PlanningRepository;
use App\Repositories\Employee\EmployeeProfileRepository;

class PlanStartQRExistRule implements ValidationRule
{
    public function __construct(protected $user_id, protected $qr_data)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plans = app(PlanningStartStopService::class)->getPlanByQrCode($this->qr_data, $this->user_id, $value, $value);
        
        if ( is_null($plans) ||$plans->isEmpty()) {
            $fail('No plan to start.');
        } elseif ($plans->count() > 1) {
            $fail('Cannot start plan, There are more than one plan.');
        } elseif ($plans->first()->plan_started) {
            $fail('Plan already started.');
        }

        $qr_data = decodeData($this->qr_data);

        setTenantDBByCompanyId($qr_data['company_id']);

        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($this->user_id);

        $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

        if ($plans->count() > 1) {
            $fail('Cannot start plan, Please stop past plan.');
        }


    }
}
