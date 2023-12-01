<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use App\Models\Company\Employee\EmployeeHolidayCount;
use App\Models\Company\Absence\Absence;

class EmployeeHolidayBalanceRule implements ValidationRule
{

    public function __construct(protected $employee_id)
    {
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        foreach ($value as $index => $data) {
            if (array_key_exists('hours', $data)) {
            $hours = $data['hours'];
            $holidayCodeId = $data['holiday_code'];

                // Fetch holiday count for the specified employee and holiday code
                $holidayCount = EmployeeHolidayCount::where('employee_id', $this->employee_id)
                    ->where('holiday_code_id', $holidayCodeId)
                    ->where('count', '>', 0)
                    ->value('count');

                // Fetch absences with hours for the specified employee and holiday code
                $absences = Absence::with(['absenceHours' => function ($query) use ($holidayCodeId) {
                    $query->select('absence_id', 'hours')
                        ->where('holiday_code_id', $holidayCodeId)
                        ->where('hours', '>', 0);
                }])
                    ->where('employee_profile_id', $this->employee_id)
                    ->get(['id', 'employee_profile_id']);

                // Flatten and pluck the hours data
                $hoursData = $absences->pluck('absenceHours')->flatten()->pluck('hours');

                // dd($hoursData->sum()+1);
                if ($hoursData->sum() > 0) {
                    // Use Laravel Collections to sum the hours
                    $totalAbsenceHours = $hoursData->sum() + $hours;

                    // Check if the total absence hours exceed the holiday count
                    if ($holidayCount < $totalAbsenceHours) {
                        $fail("Holiday balance not available from $attribute.$index holiday code");
                    }
                }
            }
        }
    }
}
