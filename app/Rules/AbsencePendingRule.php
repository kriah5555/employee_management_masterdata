<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Company\Absence\Absence;

class AbsencePendingRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $absence = Absence::find($value);

        if ($absence->absence_status != config('absence.PENDING')) {
            $fail('Absence manager can only be changes when absence is in pending.');
        }   
    }
}
