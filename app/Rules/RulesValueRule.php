<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Rule\Rule;

class RulesValueRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $rule = request()->route('rule');
        $this->validateRuleValue($rule, $value);
        if (!$this->validateRuleValue($rule, $value)) {
            $fail('Incorrect rule value.');
        }
    }

    public function validateRuleValue(Rule $rule, $value)
    {
        if (
            ($rule->value_type == 1 && ($value < 0 || $value > 600)) ||
            ($rule->value_type == 2 && ($value < 0 || $value > 24)) ||
            ($rule->value_type == 3 && ($value < 0 || $value > 365))
        ) {
            return false;
        }
        return true;
    }
}