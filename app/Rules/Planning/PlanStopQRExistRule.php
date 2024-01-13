<?php

namespace App\Rules\Planning;

use Closure;
use App\Repositories\Planning\PlanningRepository;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Repositories\Employee\EmployeeProfileRepository;

class PlanStopQRExistRule implements ValidationRule
{
    public function __construct(protected $user_id, protected $time)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $qr_data = decodeData($value);
        
        setTenantDBByCompanyId($qr_data['company_id']);
        
        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($this->user_id);
        
        $plans            = app(PlanningRepository::class)->getStartedPlanForEmployee($employee_profile->id, $qr_data['location_id']);

        if ($plans->isEmpty()) {
            $fail('No plan to stop');
        } elseif ($plans->count() > 1) {
            $fail('Cannot stop plan, There are more than one plan active');
        }

    }
}
