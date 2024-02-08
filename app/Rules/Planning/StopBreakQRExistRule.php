<?php

namespace App\Rules\Planning;

use Closure;
use App\Repositories\Planning\PlanningRepository;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Services\Planning\PlanningStartStopService;
use App\Repositories\Employee\EmployeeProfileRepository;

class StopBreakQRExistRule implements ValidationRule
{
    public function __construct(protected $user_id)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $qr_data = decodeData($value);

        setTenantDBByCompanyId($qr_data['company_id']);

        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($this->user_id);

        $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

        $current_date_time = strtotime(date('Y-m-d H:i'));
        if ($plans->count() > 1) { # check if more than one plan is started
            $fail('Cannot stop break, Please stop past plan.');
        } elseif (($plans->count() == 1 && !$plans->first()->break_started) || $plans->count() == 0) {
            $fail('No break to stop.');
        }
    }
}
