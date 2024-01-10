<?php

namespace App\Services\Company\Absence;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use Exception;
use DateTime;

class AbsenceService
{
    public function deleteAbsenceRelatedData(Absence $absence)
    {
        try {
            $absence->absenceHours()->delete(); # Delete related AbsenceHours

            $absence->absenceDates()->delete(); # Delete related AbsenceDates

            return ;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createAbsenceRelatedData(Absence $absence, $absence_hours, $dates_data, $plan_ids = [])
    {
        try {
            $absence->absenceHours()->createMany($absence_hours);

            $absence->absenceDates()->create($dates_data);

            if (!empty($plan_ids)) {
                $absence->plans()->sync($plan_ids);
            }

            return $absence;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateAbsenceStatus(Absence $absence, $status)
    {
        try {
                if (array_key_exists($status, config('absence.STATUS'))) {
                    $absence->update(['absence_status' => $status]);
                    // if ($status == config('absence.CANCEL') || $status == config('absence.REJECT')) {
                    //     $this->deleteAbsenceRelatedData($absence);
                    // }
                }
                return $absence;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAbsenceFormattedDataToSave($details, $absence_status = '')
    {
        $absence_status            = $absence_status != '' ? $absence_status : config('absence.PENDING');
        $details['absence_status'] = $absence_status;
        $holiday_code_counts       = $details['holiday_code_counts'];
        $duration_type             = $details['duration_type'];
        $absence_hours_data        = [];

        foreach ($holiday_code_counts as $index => $holiday_code_count) {
            $absence_hours_data[$index] = [
                'holiday_code_id' => $holiday_code_count['holiday_code'],
                'hours'           => $this->getCalculateAbsenceHours($details, $holiday_code_count),
                'duration_type'   => (in_array($holiday_code_count['duration_type'], [config('absence.FIRST_HALF'), config('absence.SECOND_HALF')])
                                    && in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'), config('absence.FIRST_AND_SECOND_HALF')])) # if the duration type is forts and second half with multiple holiday codes or  it is first and second half
                                    ? $holiday_code_count['duration_type'] : $duration_type,
            ];

            if ($this->breakHolidayCodeCountLoopCondition($index, $duration_type)) {
                break;
            }
        }

        $dates_data = [
            'dates'      => json_encode($details['dates']),
            'dates_type' => $details['duration_type'] == config('absence.MULTIPLE_DATES') ? config('absence.DATES_FROM_TO') : config('absence.DATES_MULTIPLE')
        ];

        return [
            'absence_hours_data' => $absence_hours_data,
            'dates_data'         => $dates_data,
            'details'            => $details
        ];
    }

    public function breakHolidayCodeCountLoopCondition($index, $duration_type)
    {
        return 
            in_array($duration_type, [
                config('absence.MULTIPLE_DATES'), # if multiple dates it will have from and to date so only one holiday code
                config('absence.FIRST_HALF'), # if first half it will have single code
                config('absence.SECOND_HALF'), # if second half it will have single code
                config('absence.FULL_DAYS'), # if full day it will have single code
            ])
            || ($index == 2 && config('absence.FIRST_AND_SECOND_HALF')); # if it is first nd second half then there will be two holiday codes
    }

    public function getCalculateAbsenceHours($details, $holiday_code_count, $with_date_calculates = false) # if $with_date_calculates => true it will return the hours * days else will return hours
    {
        $hours         = 0;
        $duration_type = $details['duration_type'];
        $days          = $with_date_calculates ? count($details['dates']) : 1;
        if ($duration_type == config('absence.MULTIPLE_DATES')) { # if form and to dates are given single holiday code
            $fromDate = new DateTime($details['dates']['from_date']);
            $toDate   = new DateTime($details['dates']['to_date']);
            $interval = $fromDate->diff($toDate);
            $days     = $with_date_calculates ? $interval->days + 1 : 1;
            $hours    = config('constants.DAY_HOURS');
        } elseif ($duration_type == config('absence.FIRST_AND_SECOND_HALF')) { # there will be only one holiday code
            $hours = (config('constants.DAY_HOURS') / 2);
        } elseif (in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES')])) { # if it is multiple codes the use the hours provided by user
            $hours = $holiday_code_count['hours'];
        } elseif (in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF')])) { # if it is multiple holiday codes with first or second half
            $hours = ($holiday_code_count['duration_type'] == '') ? $holiday_code_count['hours']: (config('constants.DAY_HOURS') / 2); # if duration type is set in the holiday codes as first or second half the use default hours else use hours provided by user
        } elseif (in_array($duration_type, [config('absence.FIRST_HALF'), config('absence.SECOND_HALF')])) { # will holiday code with no hours
            $hours = (config('constants.DAY_HOURS') / 2);
        } elseif (in_array($duration_type, [config('absence.FULL_DAYS')])) { # will fll day will have only one holiday coe with on hours
            $hours = config('constants.DAY_HOURS');
        }
        return $hours * $days;
    }

    public function deleteAbsence(Absence $absence)
    {
        $absence->absenceHours()->delete();
        $absence->absenceDates()->delete();
        $absence->delete();
        return;
    }

    public function changeReportingManager($absence, $responsible_person_id)
    {
        return $absence->update(['manager_id' => $responsible_person_id]);
    }
}
