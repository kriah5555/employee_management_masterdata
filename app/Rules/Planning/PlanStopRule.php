<?php

namespace App\Rules\Planning;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;
use App\Services\WorkstationService;
use App\Models\Planning\PlanningBase;

class PlanStopRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $plan = PlanningBase::find($value);
        if (!$plan->plan_started) {
            $fail("Plan not started");
        }
    }
}
