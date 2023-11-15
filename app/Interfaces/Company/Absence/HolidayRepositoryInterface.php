<?php

namespace App\Interfaces\Company\Absence;

use App\Models\Company\Absence\Absence;

interface HolidayRepositoryInterface
{
    public function getHolidays($status = ''); # 1 => pending, 2 => approved, 3 => Rejected, 4 => Cancelled

    public function getHolidayById(string $companyId);

    public function deleteHoliday(Absence $absence);

    public function createHoliday(array $details);

    public function updateHoliday(Absence $absence, array $updatedDetails);
}
