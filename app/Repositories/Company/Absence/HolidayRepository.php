<?php

namespace App\Repositories\Company\Absence;

use App\Interfaces\Company\Absence\HolidayRepositoryInterface;
use App\Models\Company\Absence\Absence;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;

class HolidayRepository implements HolidayRepositoryInterface
{
    public function getHolidays($status = '') # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        $query = Absence::query();
        $query->where('shift_type', config('constants.HOLIDAY'));
        if ($status != '' && array_key_exists($status, config('constants.ABSENCE_STATUS'))) {
            $query->where('absence_status', $status);
        }
        return $query->get();
    }

    public function getHolidayById(string $absenceId, array $relations = [])
    {
        return Absence::with($relations)->where('shift_type', config('constants.HOLIDAY'))->findOrFail($absenceId);
    }

    public function deleteHoliday(Absence $absence)
    {
        if ($absence->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete');
        }
    }

    public function createHoliday(array $details)
    {
        $details['shift_type'] = config('constants.HOLIDAY');
        return Absence::create($details);
    }

    public function updateHoliday(Absence $absence, array $updatedDetails)
    {
        $updatedDetails['shift_type'] = config('constants.HOLIDAY');
        if ($absence->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update');
        }
    }
}
