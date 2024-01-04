<?php

namespace App\Repositories\Company\Absence;

use App\Interfaces\Company\Absence\HolidayRepositoryInterface;
use App\Models\Company\Absence\Absence;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;

class HolidayRepository implements HolidayRepositoryInterface
{
    public function getHolidays($employee_id = '', $status = '') # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        $query = Absence::query();
        $query->where('absence_type', config('absence.HOLIDAY'));
        if ($status != '') {
            $query->where('absence_status', $status);
        }
        if ($employee_id != '') {
            $query->where('employee_profile_id', $employee_id);
        }
        $query->with(['absenceDates', 'absenceHours', 'employee', 'manager']);
        return $query->get();
    }

    public function getHolidayById(string $absenceId, array $relations = [])
    {
        return Absence::with($relations)->where('absence_type', config('absence.HOLIDAY'))->findOrFail($absenceId);
    }

    public function createHoliday(array $details)
    {
        $details['absence_type'] = config('absence.HOLIDAY');
        return Absence::create($details);
    }

    public function updateHoliday(Absence $absence, array $updatedDetails)
    {
        $updatedDetails['absence_type'] = config('absence.HOLIDAY');
        if ($absence->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update');
        }
    }
}
