<?php

namespace App\Repositories\Company\Absence;

use App\Interfaces\Company\Absence\LeaveRepositoryInterface;
use App\Models\Company\Absence\Absence;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;

class LeaveRepository implements LeaveRepositoryInterface
{
    public function getLeaves($employee_id = '', $status = '') # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled
    {
        $query = Absence::query();
        $query->where('absence_type', config('absence.LEAVE'));
        if ($status != '') {
            $query->where('absence_status', $status);
        }
        if ($employee_id != '') {
            $query->where('employee_profile_id', $employee_id);
        }
        $query->with(['absenceDates', 'absenceHours']);
        return $query->get();
    }

    public function getLeaveById(string $absenceId, array $relations = [])
    {
        return Absence::with($relations)->where('absence_type', config('absence.LEAVE'))->findOrFail($absenceId);
    }

    public function createLeave(array $details)
    {
        $details['absence_type'] = config('absence.LEAVE');
        return Absence::create($details);
    }

    public function updateLeave(Absence $absence, array $updatedDetails)
    {
        $updatedDetails['absence_type'] = config('absence.LEAVE');
        if ($absence->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update');
        }
    }
}
