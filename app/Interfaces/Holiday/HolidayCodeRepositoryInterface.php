<?php

namespace App\Interfaces\Holiday;

use App\Models\Holiday\HolidayCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface HolidayCodeRepositoryInterface
{
    public function getHolidayCodes(): Collection;

    public function getActiveHolidayCodes(): Collection;

    public function getHolidayCodeById(string $holidayCodeId, array $relations = []): Collection|Builder|HolidayCode;

    public function deleteHolidayCode(HolidayCode $holidayCode): bool;

    public function createHolidayCode(array $details): HolidayCode;

    public function updateHolidayCode(HolidayCode $holidayCode, array $updatedDetails): bool;

    public function updateHolidayCodeContractTypes(HolidayCode $holidayCode, array $contractTypes);
}