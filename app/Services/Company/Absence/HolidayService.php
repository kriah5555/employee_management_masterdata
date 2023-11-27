<?php

namespace App\Services\Company\Absence;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use App\Services\BaseService;
use App\Repositories\Company\Absence\HolidayRepository;
use App\Repositories\Employee\EmployeeProfileRepository;
use Exception;

class HolidayService
{
    public function __construct(protected HolidayRepository $holiday_repository)
    {
    }

    public function getHolidays($status = '') # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        try {
            return $this->holiday_repository->getHolidays($status);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getHolidayById(string $absenceId, array $relations = [])
    {
        try {
            return $this->holiday_repository->getHolidayById($absenceId, $relations);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createHoliday($details, $holiday_hours, $dates)
    {
        $holiday = $this->holiday_repository->createHoliday($details);

        if (!empty($holiday_hours)) {
            $holiday->absenceHours()->createMany($holiday_hours);
        }
        
        if ($details['multiple_dates']) {
            $holiday->absenceDates()->create([
                'dates' => $dates,
            ]);
        }

        return $holiday;
    }

    public function applyHoliday(array $details)
    {
        try {
            DB::connection('tenant')->beginTransaction();

                $details['absence_status'] = config('constants.HOLIDAY_PENDING');
                $details['shift_type']     = config('constants.HOLIDAY');
                $holiday_code_counts       = $details['holiday_code_counts'];
                $duration_type             = $details['duration_type'];
                $holiday_hours_data        = [];

                foreach ($holiday_code_counts as $holiday_code_count) {
                    $holiday_hours_data[] = [
                        'holiday_code_id' => $holiday_code_count['holiday_code'],
                        'hours'           => $holiday_code_count['hours'],
                        'duration_type'   => $duration_type,
                    ];

                    $hours = 0;
                    if ($details['duration_type'] != config('constants.HOLIDAY_MULTIPLE_HOLIDAY_CODES')) {
                        if ($details['multiple_dates']) { # if its multiple dates then check if the selected type is of full day or half day and multiply the days to hours else add default hours

                            $days = count($details['dates']);
                            $hours = (($duration_type['duration_type'] != config('constants.HOLIDAY_FULL_DAY')) 
                            ? config('constants.DAY_HOURS') / 2 : config('constants.DAY_HOURS')) * $days;
                        } else {
                            $hours = ($duration_type['duration_type'] != config('constants.HOLIDAY_FULL_DAY')) 
                            ? config('constants.DAY_HOURS') / 2 : config('constants.DAY_HOURS');
                        }
                        $holiday_hours_data['hours'] = $hours;
                        break;
                    }
                }

                $holiday = $this->createHoliday($details, $holiday_hours_data, json_encode($details['dates']));

            DB::connection('tenant')->commit();
            
            return $holiday;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateHoliday($absenceId, array $updatedDetails)
    {
        try {
            $absence = Absence::where('shift_type', config('constants.HOLIDAY'))->findOrFail($absenceId);
            return $this->holiday_repository->updateHoliday($absence, $updatedDetails);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteHoliday($absenceId)
    {
        try {
            $absence = Absence::where('shift_type', config('constants.HOLIDAY'))->findOrFail($absenceId);
            return $this->holiday_repository->deleteHoliday($absence);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getHolidayAbsenceStatusOptions()
    {
        return getValueLabelOptionsFromConfig('constants.ABSENCE_STATUS');
    }

    public function getHolidayDurationTypesOptions()
    {
        return getValueLabelOptionsFromConfig('constants.ABSENCE_DURATION_TYPE');
    }
    
    public function getOptionsToCreate()
    {
        try {
            return [
                'absence_status'        => self::getHolidayAbsenceStatusOptions(),
                'absence_duration_type' => self::getHolidayDurationTypesOptions()
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}