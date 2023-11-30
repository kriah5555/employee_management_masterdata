<?php

namespace App\Services\Company\Absence;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use App\Services\BaseService;
use App\Repositories\Company\Absence\HolidayRepository;
use App\Repositories\Employee\EmployeeProfileRepository;
use Exception;
use DateTime;

class HolidayService
{
    public function __construct(protected HolidayRepository $holiday_repository)
    {
    }

    public function getHolidays($employee_id = '',$status = '') # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            return $this->holiday_repository->getHolidays($employee_id, $status);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getHolidayById(string $holiday_id, array $relations = [])
    {
        try {
            return $this->holiday_repository->getHolidayById($holiday_id, $relations);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createHoliday($details, $holiday_hours, $dates)
    {
        try {
            $holiday = $this->holiday_repository->createHoliday($details);

            $holiday->absenceHours()->createMany($holiday_hours);
            
            $holiday->absenceDates()->create([
                'dates' => $dates,
                'dates_type' => $details['duration_type'] == config('absence.MULTIPLE_DATES') ? config('absence.DATES_FROM_TO') : config('absence.DATES_MULTIPLE')        
            ]);

            return $holiday;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateHoliday($holiday_id, $updatedDetails, $holiday_hours, $dates)
    {
        try {
            $holiday = $this->getHolidayById($holiday_id);

            $this->deleteAbsenceRelatedData($holiday); # delete old records of holiday dates and holiday hours

            $this->holiday_repository->updateHoliday($holiday, $updatedDetails);

            $holiday->absenceHours()->createMany($holiday_hours);
            
            $holiday->absenceDates()->create([
                'dates' => $dates, 
                'dates_type' => $updatedDetails['duration_type'] == config('absence.MULTIPLE_DATES') ? config('absence.DATES_FROM_TO') : config('absence.DATES_MULTIPLE')
            ]);

            return $holiday;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateHolidayStatus($holiday_id, $status)
    {
        try {
            DB::connection('tenant')->beginTransaction();
                if (array_key_exists($status, config('absence.STATUS'))) {
                    $holiday =  $this->getHolidayById($holiday_id);
                    $holiday->update(['absence_status' => $status]);
                    if ($status == config('absence.CANCEL') || $status == config('absence.REJECT')) {
                        $this->deleteAbsenceRelatedData($holiday);  
                }
            DB::connection('tenant')->commit();

                return $holiday;
            } 
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteAbsenceRelatedData(Absence $holiday) 
    {
        try {
            $holiday->absenceHours()->delete(); # Delete related AbsenceHours 

            $holiday->absenceDates()->delete(); # Delete related AbsenceDates

            return ;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getHolidayFormattedDataToSave($details, $absence_status = '')
    {
        $absence_status            = $absence_status != '' ? $absence_status : config('absence.PENDING');
        $details['absence_status'] = $absence_status;
        $holiday_code_counts       = $details['holiday_code_counts'];
        $duration_type             = $details['duration_type'];
        $holiday_hours_data        = [];

        foreach ($holiday_code_counts as $index => $holiday_code_count) {
            $holiday_hours_data[$index] = [ 
                'holiday_code_id' => $holiday_code_count['holiday_code'],
                'hours'           => $holiday_code_count['hours'],
                'duration_type'   => (in_array($holiday_code_count['duration_type'], [config('absence.FIRST_HALF'), config('absence.SECOND_HALF')]) 
                                    && in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF'), config('absence.FIRST_AND_SECOND_HALF')])) # if the duration type is forts and second half with multiple holiday codes or  it is first and second half
                                    ? $holiday_code_count['duration_type'] : $duration_type,
            ];

            $hours = 0;
            $holiday_hours_data[$index]['hours'] = $this->getCalculateHolidayHours($details, $holiday_code_count);
            if (
                in_array($duration_type, [
                    config('absence.MULTIPLE_DATES'), # if multiple dates it will have from and to date so only one holiday code
                    config('absence.FIRST_HALF'), # if first half it will have single code
                    config('absence.SECOND_HALF'), # if second half it will have single code
                    config('absence.FIRST_AND_SECOND_HALF'), # if second half it will have single code
                ]) 
                || ($index == 2 && config('absence.FIRST_AND_SECOND_HALF'))
            ) { # if it is first nd second half then there will be two holiday codes
                break;
            } 
        }
        return [
            'holiday_hours_data' => $holiday_hours_data,
            'dates'              => json_encode($details['dates']),
            'details'            => $details
        ];
    }

    
    public function getCalculateHolidayHours($details, $holiday_code_count)
    {
        $hours         = 0;
        $duration_type = $details['duration_type'];
        if ($duration_type == config('absence.MULTIPLE_DATES')) { # if form and to dates are given single holiday code
            $fromDate = new DateTime($details['dates']['from_date']);
            $toDate   = new DateTime($details['dates']['to_date']);
            $interval = $fromDate->diff($toDate);
            $days     = $interval->days + 1;
            $hours    = config('constants.DAY_HOURS') * $days;
        } elseif ($duration_type == config('absence.FIRST_AND_SECOND_HALF')) { # there will be only one holiday code 
            $hours = config('constants.DAY_HOURS') * count($details['dates']);
        } elseif (in_array($duration_type, [config('absence.MULTIPLE_HOLIDAY_CODES'), config('absence.MULTIPLE_HOLIDAY_CODES_FIRST_HALF'), config('absence.MULTIPLE_HOLIDAY_CODES_SECOND_HALF')])) {
            $hours = $holiday_code_count['hours'];
        } elseif (in_array($duration_type, [config('absence.FIRST_HALF'), config('absence.SECOND_HALF')])) {
            $hours = (config('constants.DAY_HOURS') / 2) * count($details['dates']);
        }

        return $hours;
    }



    public function applyHoliday(array $details)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $formatted_data = $this->getHolidayFormattedDataToSave($details);

                $holiday = $this->createHoliday($formatted_data['details'], $formatted_data['holiday_hours_data'], $formatted_data['dates']);

            DB::connection('tenant')->commit();
            
            return $holiday;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateAppliedHoliday($holiday_id, array $updatedDetails)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $formatted_data = $this->getHolidayFormattedDataToSave($details);

                $holiday        = this->updateHoliday($holiday_id, $formatted_data['details'], $formatted_data['holiday_hours_data'], $formatted_data['dates']);

            DB::connection('tenant')->commit();
            return $holiday;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());    
            throw $e;
        }
    }

    public function deleteHoliday($absenceId)
    {
        try {
            $absence = Absence::where('absence_type', config('absence.HOLIDAY'))->findOrFail($absenceId);
            return $this->holiday_repository->deleteHoliday($absence);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getHolidayAbsenceStatusOptions()
    {
        return getValueLabelOptionsFromConfig('absence.STATUS');
    }

    public function getHolidayDurationTypesOptions()
    {
        return getValueLabelOptionsFromConfig('absence.DURATION_TYPE');
    }
    
    public function getOptionsToCreate()
    {
        try {
            return [
                'absence_status' => self::getHolidayAbsenceStatusOptions(),
                'absence_type'   => self::getHolidayDurationTypesOptions()
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}