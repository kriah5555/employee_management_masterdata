<?php

namespace App\Services\HolidayCode;

use Illuminate\Support\Facades\DB;
use App\Models\HolidayCodes;
use App\Services\BaseService;

class HolidayCodeService extends BaseService
{
    public function __construct(HolidayCodes $holidayCodes)
    {
        parent::__construct($holidayCodes);
    }

    public function getOptionsToCreate()
    {
        return [
            'holiday_type'                      => config('constants.HOLIDAY_TYPE_OPRIONS'),
            'count_type'                        => config('constants.HOLIDAY_TYPE_OPRIONS'),
            'icon_type'                         => config('constants.HOLIDAY_TYPE_OPRIONS'),
            'consider_plan_hours_in_week_hours' => config('constants.HOLIDAY_TYPE_OPRIONS'),
            'employee_category'                 => config('constants.HOLIDAY_TYPE_OPRIONS'),
            'contract_type'                     => config('constants.HOLIDAY_TYPE_OPRIONS'),
            'carry_forword'                     => config('constants.HOLIDAY_TYPE_OPRIONS'),
        ];
    }

    public function getOptionsToEdit($holiday_code_id)
    {
        $options = $this->getOptionsToCreate();
        $options['details'] = $this->get($holiday_code_id);
        return $options;
    }
}
