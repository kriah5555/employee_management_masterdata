<?php

namespace App\Repositories\Holiday;

use App\Models\Holiday\HolidayCode;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

class HolidayCodeRepository extends BaseRepository
{

    public function __construct(HolidayCode $holidayCode)
    {
        parent::__construct($holidayCode);
    }

    public function getHolidayCodes(): Collection
    {
        return $this->model::all();
    }

    public function getActiveHolidayCodes(): Collection
    {
        return $this->model::where('status', '=', true)->get();
    }

    public function getHolidayCodeById(string $holidayCodeId, array $relations = []): Collection|Builder|HolidayCode
    {
        return $this->model::with($relations)->findOrFail($holidayCodeId);
    }

    public function deleteHolidayCode(HolidayCode $holidayCode): bool
    {
        if ($holidayCode->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete holiday code');
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
            throw new ModelUpdateFailedException('Failed to update holiday code');
        }
    }
    public function updateHolidayCodeContractTypes(HolidayCode $holidayCode, array $contractTypes)
    {
        return $holidayCode->contractTypes()->sync($contractTypes ?? []);
    }
    public function create($values): HolidayCode
    {
        return DB::transaction(function () use ($values) {
            $values['employee_category'] = json_encode($values['employee_category']); // Encode the employee_category array as JSON before saving
            $holidayCode = $this->model::create($values);
            $employee_types = $values['employee_types'] ?? [];
            $holidayCode->employeeTypes()->sync($employee_types);
            $holidayCode->linkCompanies($values['link_companies'], $values['companies'] ?? []);
            return $holidayCode;
        });
    }
}
