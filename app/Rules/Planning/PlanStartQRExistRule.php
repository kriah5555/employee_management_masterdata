<?php

namespace App\Rules\Planning;

use Closure;
use App\Repositories\Planning\PlanningRepository;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\Planning\PlanningStartStopService;
use App\Repositories\Employee\EmployeeProfileRepository;

class PlanStartQRExistRule implements ValidationRule
{
    public function __construct(protected $user_id, protected $time)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $qr_data = decodeData($this->qr_data);

        setTenantDBByCompanyId($qr_data['company_id']);

        $plans = app(PlanningStartStopService::class)->getPlanByQrCode($value, $this->user_id, $this->time, $this->time);
        
        if ( is_null($plans) ||$plans->isEmpty()) {
            $fail('No plan to start.');
        } elseif ($plans->count() > 1) {
            $fail('Cannot start plan, There are more than one plan.');
        } elseif ($plans->first()->plan_started) {
            $fail('Plan already started.');
        }

        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($this->user_id);

        $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

        if ($plans->count() > 1) {
            $fail('Cannot start plan, Please stop past plan.');
        }


    }
}
