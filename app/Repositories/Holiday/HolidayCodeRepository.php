<?php

namespace App\Repositories\Holiday;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\Holiday\HolidayCodeRepositoryInterface;
use App\Models\Holiday\HolidayCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class HolidayCodeRepository implements HolidayCodeRepositoryInterface
{
    public function getHolidayCodes(): Collection
    {
        return HolidayCode::all();
    }
    
    public function getActiveHolidayCodes(): Collection
    {
        return HolidayCode::where('status', '=', true)->get();
    }

    public function getHolidayCodeById(string $holidayCodeId, array $relations = []): Collection|Builder|HolidayCode
    {
        return HolidayCode::with($relations)->findOrFail($holidayCodeId);
    }

    public function deleteHolidayCode(HolidayCode $holidayCode): bool
    {
        if ($holidayCode->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete employee type');
        }
    }

    public function createHolidayCode(array $details): HolidayCode
    {
        return HolidayCode::create($details);
    }

    public function updateHolidayCode(HolidayCode $holidayCode, array $updatedDetails): bool
    {
        if ($holidayCode->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update employee type');
        }
    }
    public function updateHolidayCodeContractTypes(HolidayCode $holidayCode, array $contractTypes)
    {
        return $holidayCode->contractTypes()->sync($contractTypes ?? []);
    }
}