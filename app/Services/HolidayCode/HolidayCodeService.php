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
            'holiday_type'                      => $this->transformOptions(config('constants.HOLIDAY_TYPE_OPTIONS')),
            'count_type'                        => $this->transformOptions(config('constants.HOLIDAY_COUNT_TYPE_OPTIONS')),
            'icon_type'                         => $this->transformOptions(config('constants.HOLIDAY_ICON_TYPE_OPTIONS')),
            'consider_plan_hours_in_week_hours' => $this->transformOptions(config('constants.YES_OR_NO_OPTIONs')),
            'employee_category'                 => $this->transformOptions(config('constants.HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS')),
            'contract_type'                     => $this->transformOptions(config('constants.HOLIDAY_CONTRACT_TYPE_OPTIONS')),
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
        // Get options for creating
        $options = $this->getOptionsToCreate();

        // Get holiday details
        $details = $this->get($holiday_code_id);

        // Define a mapping of keys to transform
        $keysToTransform = [
            'holiday_type',
            'count_type',
            'icon_type',
            'consider_plan_hours_in_week_hours',
            'employee_category',
            'contract_type',
            'carry_forword',
        ];

        // Process each key
        foreach ($keysToTransform as $key) {
            if (isset($details[$key])) {
                if ($key === 'employee_category') {
                    // Transform employee categories
                    $employeeCategories = json_decode($details[$key], true);
                    $employeeCategoryLabels = [];

                    foreach ($employeeCategories as $employeeCategory) {
                        $employeeCategoryLabels[] = [
                            'value' => $employeeCategory,
                            'label' => config('constants.HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS')[$employeeCategory] ?? null,
                        ];
                    }

                    $details[$key] = $employeeCategoryLabels;
                } else {
                    // Transform other keys
                    $details[$key] = [
                        'value' => $details[$key],
                        'label' => $options[$key][$details[$key] - 1]['label'] ?? null,
                    ];
                }
            }
        }

        // Add the modified "details" back to the response
        $options['details'] = $details;

        return $options;
    }

    public function create($values)
    {
        
        $values['employee_category'] = json_encode($values['employee_category']); // Encode the employee_category array as JSON before saving
        $holidayCode = $this->model::create($values);// Create the holiday code record
        return $holidayCode;
    }
}
