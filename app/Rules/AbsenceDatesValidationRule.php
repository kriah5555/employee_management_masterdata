<?php

namespace App\Rules;

use Closure;
use App\Rules\FromAndToDateRule;
use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use Illuminate\Contracts\Validation\ValidationRule;

class AbsenceDatesValidationRule implements ValidationRule
{
    public function __construct(protected $employee_profile_id, protected $duration_type, protected $absence_id = '')
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->duration_type == config('absence.MULTIPLE_DATES')) { # will have from and to date
            if (isset($value['from_date']) && isset($value['to_date'])) {
                $fromAndToDateRule = new FromAndToDateRule($value['from_date'], $value['to_date']);
                $fromAndToDateRule->validate($attribute, $value, $fail);

                $absence_applied_dates = getDatesArray($value['from_date'], $value['to_date']);
            } else {
                $fail("Please select From date and to date.");
                return;
            }
        } else {
            $absence_applied_dates = $value;
        }

        $query = Absence::query();

        $query->where('employee_profile_id', $this->employee_profile_id);

        if (!empty($this->absence_id)) { # for edit flow
            $query->where('id', '!=', $this->absence_id);
        }

        $query->with(['absenceDates']);

        $absence_data = $query->get();
        $absence_dates_array = $absence_data->pluck('absenceDates.absence_dates_array')->flatten()->all();

        # employee can apply leave on only second half if the leave is applied on first half validation and vise versa
        $overlapping_dates_on_error = true;
        if (in_array($this->duration_type, [config('absence.FIRST_HALF'), config('absence.SECOND_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF')])) {

            $query1 = clone $query;
            if (in_array($this->duration_type, [config('absence.SECOND_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF')])) {
                $query1->whereIn('duration_type', [config('absence.SECOND_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF')]);
            } elseif (in_array($this->duration_type, [config('absence.FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF')])) {
                $query1->whereIn('duration_type', [config('absence.FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF')]);
            }

            $absence_data_with_half_day_dates = $query1->get();
            $absence_data_with_half_day_dates = $absence_data_with_half_day_dates->pluck('absenceDates.absence_dates_array')->flatten()->all();
            $overlapping_dates_on_error = !empty(array_intersect($absence_applied_dates, $absence_data_with_half_day_dates));
        }

        $overlapping_dates = array_intersect($absence_applied_dates, $absence_dates_array);
        if (!empty($overlapping_dates) && $overlapping_dates_on_error) {
            $overlapping_dates = implode(', ', $overlapping_dates);
            $fail("Absence already applied for dates {$overlapping_dates}");
        }

    }
}
