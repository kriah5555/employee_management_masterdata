<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Company\Employee\EmployeeHolidayCount;
use App\Models\Company\Absence\Absence;
use App\Services\Company\Absence\AbsenceService;
use App\Repositories\Holiday\HolidayCodeRepository;

class EmployeeHolidayBalanceRule implements ValidationRule
{

    public function __construct(protected $employee_id, protected $duration_type, protected $absence_id = '')
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($this->duration_type == config('absence.MULTIPLE_DATES') && (!isset(request()->dates['from_date']) || !isset(request()->dates['to_date']))) { # will have from and to date
            return; 
        }

        $absenceHoursData = collect($value)->map(function ($data) {
            return [
                'holiday_code' => $data['holiday_code'],
                'hours' => app(AbsenceService::class)->getCalculateAbsenceHours(request(), $data, true),
            ];
        });

        $absenceHoursValidate = $absenceHoursData->groupBy('holiday_code')->map(function ($group) {
            return [
                'holiday_code' => $group->first()['holiday_code'],
                'hours' => $group->sum('hours'),
            ];
        }); # to make unique holiday code with count

        foreach ($absenceHoursValidate as $index => $data) {
            if (array_key_exists('hours', $data)) {
                $requested_hours = $data['hours'];
                $holidayCodeId  = $data['holiday_code'];

                // Fetch holiday count for the specified employee and holiday code
                $holiday_code_Count = EmployeeHolidayCount::where('employee_id', $this->employee_id)
                    ->where('holiday_code_id', $holidayCodeId)
                    ->where('count', '>', 0)
                    ->value('count');

                // Fetch absences with hours for the specified employee and holiday code
                $absences = Absence::with(['absenceHours' => function ($query) use ($holidayCodeId) {
                    $query->select('absence_id', 'hours')
                        ->where('holiday_code_id', $holidayCodeId)
                        ->where('hours', '>', 0);
                    }])
                    ->where('absence_type', config('absence.HOLIDAY'))
                    ->where('employee_profile_id', $this->employee_id);

                if (!empty($this->absence_id)) {
                    $absences->where('id', '!=', $this->absence_id);
                }

                $absences = $absences->get(['id', 'employee_profile_id']);

                $employee_holiday_hours_used = $absences->pluck('absenceHours')->flatten()->pluck('hours');
                $employee_holiday_hours_used = 0;

                foreach ($absences as $absence) {
                    $employee_holiday_hours_used = count($absence->absenceDates->absence_dates_array) * $absences->pluck('absenceHours')->flatten()->pluck('hours')->sum();
                }

                $remaining_holiday_count = $holiday_code_Count- $employee_holiday_hours_used;

                // Check if the total absence hours exceed the holiday count
                if ($requested_hours > $remaining_holiday_count) {
                    $holiday_code = app(HolidayCodeRepository::class)->getHolidayCodeById($holidayCodeId);
                    $fail("Holiday balance not available for '$holiday_code->holiday_code_name'");
                }
            }

            if (app(AbsenceService::class)->breakHolidayCodeCountLoopCondition($index, $this->duration_type)) { # will break the loop according to the duration type holiday count needed
                break;
            }
        }
    }
}
