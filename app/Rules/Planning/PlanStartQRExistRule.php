<?php

namespace App\Rules\Planning;

use Closure;
use App\Repositories\Planning\PlanningRepository;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Repositories\Employee\EmployeeProfileRepository;

class PlanStartQRExistRule implements ValidationRule
{
    public function __construct(protected $user_id, protected $qr_data)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $qr_data = decodeData($this->qr_data);

        setTenantDBByCompanyId($qr_data['company_id']);

        $employee_profile = app(EmployeeProfileRepository::class)->getEmployeeProfileByUserId($this->user_id);
        
        $plans            = app(PlanningRepository::class)->getPlans('', '', $qr_data['location_id'], '', '', $employee_profile->id, [], date('d-m-Y') . ' ' . $value . ':00', date('d-m-Y') . ' ' . $value . ':00');
        if ($plans->isEmpty()) {
            $fail('No plan to start');
        } elseif ($plans->count() > 1) {
            $fail('Cannot start plan, There are more than one plan');
        } elseif ($plans->first()->plan_started) {
            $fail('Plan already started');
        }
    }
}
