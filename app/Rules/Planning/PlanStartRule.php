<?php

namespace App\Rules\Planning;

use Closure;
use Illuminate\Validation\Rule;
use App\Services\WorkstationService;
use App\Models\Planning\PlanningBase;
use App\Repositories\Planning\PlanningRepository;
use Illuminate\Contracts\Validation\ValidationRule;

class PlanStartRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plan = PlanningBase::find($value);

        if ($plan) {
            $started_plans = app(PlanningRepository::class)->getStartedPlanForEmployee($plan->employee_profile_id, '', $value);
            if ($started_plans->count() > 0) {
                $location    = $started_plans->first()->location->location_name;
                $workstation = $started_plans->first()->workstation->workstation_name;
                $fail("Please stop plan at location: $location, workstation: $workstation");
                return ;
            }

            if (strtotime($plan->start_date_time) > strtotime(date('Y-m-d H:i')) || strtotime($plan->end_date_time) < strtotime(date('Y-m-d H:i'))) {
                $fail("This plan cannot be started at this time");
            }

            if ($plan->plan_started) {
                $fail("Plan already started");
            }
        }
    }
}
