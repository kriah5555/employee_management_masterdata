<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Contract\ContractTemplate;

class ContractTemplateUniqueCombinationRule implements ValidationRule
{
    public function __construct(protected $exclude_id = null)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Build the query to check uniqueness while excluding the current record
        $query = ContractTemplate::query();
        $query->where([
            'employee_type_id' => request()->input('employee_type_id'),
            // 'social_secretary_id' => request()->input('social_secretary_id', null),
            'language'         => request()->input('language'),
        ]);

        // Exclude the current record if it's being updated
        if ($this->exclude_id !== null) {
            $query->where('id', '!=', $this->exclude_id);
        }
        if ($query->exists()) {
            $fail('The template with the specified criteria already exists.');
        }
    }
}
