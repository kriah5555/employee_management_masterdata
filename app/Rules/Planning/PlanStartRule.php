<?php

namespace App\Rules\Planning;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use App\Services\WorkstationService;
use App\Models\Planning\PlanningBase;

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
        if (strtotime($plan->start_date_time) < strtotime(date('Y-m-d H:i')) || strtotime($plan->end_date_time) < strtotime(date('Y-m-d H:i'))) {
            $fail("This plan cannot be started at this time");
        }
        if ($plan->plan_started) {
            $fail("Plan already started");
        }
    }
}
