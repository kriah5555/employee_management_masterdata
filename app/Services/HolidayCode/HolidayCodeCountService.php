<?php

namespace App\Services\HolidayCode;

use Illuminate\Support\Facades\DB;
use App\Models\HolidayCodes;
use App\Services\BaseService;

class HolidayCodeCountService extends BaseService
{
    public function __construct(HolidayCodes $holidayCodes)
    {
        parent::__construct($holidayCodes);
    }
}