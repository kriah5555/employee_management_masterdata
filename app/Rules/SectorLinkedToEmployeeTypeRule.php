<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Sector\Sector;

class SectorLinkedToEmployeeTypeRule implements ValidationRule
{
    public function __construct(protected $employee_type_id)
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!empty($value)) {
            $exists = Sector::where('id', $value)
                ->whereHas('employeeTypes', function ($query) {
                    $query->where('employee_types.id', $this->employee_type_id);
                })
                ->exists();

            if (!$exists) {
                $fail("The :attribute is not linked with the selected employee type.");
            }
        }
    }
}
