<?php

namespace App\Rules;

use Closure;
use App\Services\DateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\Validation\ValidationRule;

class AbsenceDatesValidationRule implements ValidationRule
{
    public function __construct(protected $employee_profile_id, protected $duration_type, protected $absence_id = 0)
    {
    }
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $databaseDates = $this->getDatesAsPerEmployee($this->employee_profile_id, $this->absence_id);

        foreach ($databaseDates as $oldDate) {
            if ($oldDate['dates_type'] == config('absence.DATES_FROM_TO')) {
                $oldDate['dates'] = $this->dateArrayFormat($oldDate);
            }

            $oldDatesArray = json_decode(json_encode($oldDate['dates']), true);

            if ($this->duration_type == config('absence.MULTIPLE_DATES')) {
                $value = [$attribute => $value];
                $value = $this->dateArrayFormat($value);
            }

            foreach ($value as $newDate) {
                if (in_array($newDate, $oldDatesArray)) {
                    if ($oldDate['duration_type'] == config('absence.FIRST_HALF')) {
                        $this->validationFail(config('absence.SECOND_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'), $fail);
                    } elseif ($oldDate['duration_type'] == config('absence.SECOND_HALF')) {
                        $this->validationFail(config('absence.FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), $fail);
                    } elseif ($oldDate['duration_type'] == config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF')) {
                        $this->validationFail(config('absence.SECOND_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'), $fail);
                    } elseif ($oldDate['duration_type'] == config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF')) {
                        $this->validationFail(config('absence.FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), $fail);
                    } elseif (in_array($oldDate['duration_type'], [config('absence.MULTIPLE_HOLIDAY_CODES'), config('absence.FIRST_AND_SECOND_HALF'), config('absence.MULTIPLE_DATES'), config('absence.FULL_DAYS')])) {
                        $fail("The selected dates have already been designated as holidays. Please verify your choices and try again!");
                    }
                }
            }
        }
    }

    public function validationFail(string $firstCondition, string $secondCondition, Closure $fail)
    {
        if (!in_array($this->duration_type, [$firstCondition, $secondCondition])) {
            $fail("The selected dates have already been designated as holidays. Please verify your choices and try again!");
        }
    }

    public function getDatesAsPerEmployee($employee_profile_id, $absence_id)
    {
        $query = DB::table('absence')
            ->join('absence_dates', 'absence.id', '=', 'absence_dates.absence_id')
            ->select('absence_dates.dates', 'absence_dates.dates_type', 'absence.duration_type')
            ->where('absence.employee_profile_id', $employee_profile_id);

        $query->when($absence_id > 0, function ($query) use ($absence_id) {
            $query->where('absence.id', '!=', $absence_id);
        });

        $result = $query->get();

        $oldData = [];
        foreach ($result as $index => $data) {
            $oldData[$index] = [
                'dates'          => json_decode(json_decode($data->dates, true)),
                'dates_type'     => $data->dates_type,
                'duration_type'  => $data->duration_type
            ];
        }
        return $oldData;
    }

    public function dateArrayFormat($oldDate)
    {
        $fromDate = '';
        $toDate = '';
        if (is_array($oldDate['dates'])) {
            $fromDate = $oldDate['dates']['from_date'];
            $toDate = $oldDate['dates']['to_date'];
        } elseif (is_object($oldDate['dates']) && property_exists($oldDate['dates'], 'from_date')) {
            $fromDate = $oldDate['dates']->from_date;
            $toDate = $oldDate['dates']->to_date;
        }
        return (new DateService())->getDatesArray($fromDate, $toDate);
    }
}
