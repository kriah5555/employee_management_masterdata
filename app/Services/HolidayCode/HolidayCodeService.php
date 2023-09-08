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
            'holiday_type'                      => $this->transformOptions(config('constants.HOLIDAY_TYPE_OPRIONS')),
            'count_type'                        => $this->transformOptions(config('constants.HOLIDAY_COUNT_TYPE_OPRIONS')),
            'icon_type'                         => $this->transformOptions(config('constants.HOLIDAY_ICON_TYPE_OPRIONS')),
            'consider_plan_hours_in_week_hours' => $this->transformOptions(config('constants.YES_OR_NO_OPTIONs')),
            'employee_category'                 => $this->transformOptions(config('constants.HOLIDAY_EMPLOYEE_CATEGORY_OPRIONS')),
            'contract_type'                     => $this->transformOptions(config('constants.HOLIDAY_CONTRACT_TYPE_OPRIONS')),
            'carry_forword'                     => $this->transformOptions(config('constants.YES_OR_NO_OPTIONs')),
        ];
    }

    private function transformOptions($options)
    {
        return array_map(function ($key, $value) {
            return ['value' => $key, 'label' => $value];
        }, array_keys($options), $options);
    }

    public function getOptionsToEdit($holiday_code_id)
{
    $options = $this->getOptionsToCreate();
    $details = $this->get($holiday_code_id);
    
    // Transform specific keys in the "details" array
    $keysToTransform = [
        'holiday_type',
        'count_type',
        'icon_type',
        'consider_plan_hours_in_week_hours',
        'employee_category',
        'contract_type',
        'carry_forword',
    ];
    
    foreach ($keysToTransform as $key) {
        if (isset($details[$key])) {
            $details[$key] = [
                'value' => $details[$key],
                'label' => $options[$key][$details[$key] - 1]['label'] ?? null,
            ];
        }
    }
    
    // Add the modified "details" back to the response
    $options['details'] = $details;
    
    return $options;
}
}
