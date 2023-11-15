<?php

namespace App\Services\Company\Absence;

use Illuminate\Support\Facades\DB;
use App\Models\Company\Absence\Absence;
use App\Services\BaseService;
use App\Repositories\Company\Absence\HolidayRepository;
use Exception;

class HolidayService
{
    public function __construct(protected HolidayRepository $holiday_repository )
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

    public function createHoliday(array $details)
    {
        try {
            $holiday = $this->holiday_repository->createHoliday($details);
            
            return $holiday;

        } catch (Exception $e) {
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