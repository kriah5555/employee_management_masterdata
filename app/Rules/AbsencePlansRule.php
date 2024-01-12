<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AbsencePlansRule implements ValidationRule
{
    public function __construct(protected $dates, protected $duration_type)
    {

    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $absence_applied_dates = [];
        if (!empty($this->dates)) {
            if ($this->duration_type == config('absence.MULTIPLE_DATES')) { # will have from and to date
                if (isset($this->dates['from_date']) && isset($this->dates['to_date'])) {
                    $absence_applied_dates = getDatesArray($this->dates['from_date'], $this->dates['to_date']);
                }
            } else {
                $absence_applied_dates = $value;
            }
        }

        if (!empty($absence_applied_dates)) {

        }
    }
}
