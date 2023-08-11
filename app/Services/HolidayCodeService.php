<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\HolidayCodes;

class HolidayCodeService
{
    public function getHolidayCodeDetails($id)
    {
        return HolidayCodes::findOrFail($id);
    }

    public function getAllHolidayCodes()
    {
        return HolidayCodes::all();
    }

    public function getActiveHolidayCodes()
    {
        return HolidayCodes::where('status', '=', true)->get();
    }

    public function createNewHolidayCode($values)
    {
        try {
            $holiday_codes = HolidayCodes::create($values);
            return $holiday_codes ;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateHolidayCode(HolidayCodes $holiday_code, $values)
    {
        try {
            $holiday_code->update($values);
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
