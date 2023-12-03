<?php

namespace App\Interfaces\Company\Absence;

use App\Models\Company\Absence\Absence;

interface LeaveRepositoryInterface
{
    public function getLeaves($employee_id = '', $status = ''); # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled

    public function getLeaveById(string $companyId);

    public function createLeave(array $details);

    public function updateLeave(Absence $absence, array $updatedDetails);
}
