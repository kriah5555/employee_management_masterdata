<?php

namespace App\Services\Holiday;

use Illuminate\Support\Facades\DB;
use App\Models\Holiday\HolidayCodes;
use App\Models\Company;
use App\Services\BaseService;
use App\Services\EmployeeType\EmployeeTypeService;

class HolidayCodeService extends BaseService
{
    protected $employeeTypeService;

    public function __construct(HolidayCodes $holidayCodes)
    {
        parent::__construct($holidayCodes);
        $this->employeeTypeService = app(EmployeeTypeService::class);

    }

    # if the $args['with'] is passed then the count will be returned according to days or hours default in db the count will be stored as hours only
    public function getAll(array $args = [])
    {
        $objects = $this->model::all();
        if (isset($args['with'])) {
            // Create a new collection to store modified objects
            $modifiedObjects = collect([]);

            // Loop through each object and modify its count attribute
            $objects->each(function ($object) use (&$modifiedObjects) {
                $modifiedObject = clone $object; // Create a clone to avoid modifying the original
                if ($modifiedObject->count_type == 2) {
                    // If count_type is 2, modify the count value
                    $modifiedObject->count = $modifiedObject->count / config('constants.DAY_HOURS');
                }
                // You can add more modifications or conditions here if needed

                // Add the modified object to the new collection
                $modifiedObjects->push($modifiedObject);
            });

            return $modifiedObjects;
        } else {
            return $objects;
        } 
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
            'type'                              => $this->transformOptions(config('constants.HOLIDAY_TYPE_OPTIONS')),
            'employee_types'                    => $this->employeeTypeService->getEmployeeTypeOptions(),
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
        $details = $this->get($holiday_code_id, ['employeeTypesValue']);
        $details['count'] = $details['count_type'] == 2 ? $details['count'] / config('constants.DAY_HOURS') : $details['count'];
        
        // Define a mapping of keys to transform
        $keysToTransform = [
            'holiday_type'                      => config('constants.HOLIDAY_TYPE_OPTIONS'),
            'icon_type'                         => config('constants.HOLIDAY_ICON_TYPE_OPTIONS'),
            'consider_plan_hours_in_week_hours' => config('constants.YES_OR_NO_OPTIONs'),
            'employee_category'                 => config('constants.HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS'),
            'contract_type'                     => config('constants.HOLIDAY_CONTRACT_TYPE_OPTIONS'),
            'count_type'                        => config('constants.HOLIDAY_COUNT_TYPE_OPTIONS'),
            'type'                              => config('constants.HOLIDAY_TYPE_OPTIONS'),
        ];

        // Process each key
        foreach ($keysToTransform as $key => $value) {
            if (isset($details[$key])) {
                // Transform employee categories
                if ($key === 'employee_category') {
                    $details[$key] = array_map(function ($employeeCategory) use ($value) {
                        return [
                            'value' => $employeeCategory,
                            'label' => $value[$employeeCategory] ?? null,
                        ];
                    }, json_decode($details[$key], true));
                } else {
                    // Transform other keys
                    $details[$key] = [
                        'value' => $details[$key],
                        'label' => $keysToTransform[$key][$details[$key]] ?? null,
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
        try {
            DB::beginTransaction();
            $values['employee_category'] = json_encode($values['employee_category']); // Encode the employee_category array as JSON before saving
            $holidayCode                 = $this->model::create($values);// Create the holiday code record
            $employee_types              = $values['employee_types'] ?? [];
            $holidayCode->employeeTypes()->sync($employee_types);
            DB::commit();
            return $holidayCode;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }

    }

    public function getHolidayCodesWithStatusForCompany($company_id)
    {
        try {
            // Get all holiday codes
            $allHolidayCodes = $this->model::where('status', true)->get();

            // Get the IDs of holiday codes linked to the company
            $linkedHolidayCodesIds = $this->getAllHolidayCodesLinkedToCompany($company_id);

            // Format the holiday codes with their status
            $formattedHolidayCodes = $allHolidayCodes->map(function ($holidayCode) use ($linkedHolidayCodesIds) {
                return [
                    'value' => $holidayCode->id,
                    'label' => $holidayCode->holiday_code_name,
                    'status' => in_array($holidayCode->id, $linkedHolidayCodesIds),
                ];
            });

            return $formattedHolidayCodes->toArray();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getAllHolidayCodesLinkedToCompany($company_id)
    {
        $company = Company::findOrFail($company_id);
        return $company->holidayCodes()->pluck('holiday_codes.id')->toArray();
    }

    public function updateHolidayCodesToCompany($company_id, $values) 
    {
        try {
            DB::beginTransaction();

                $company = Company::findOrFail($company_id);
                $holiday_code_ids = $values['holiday_code_ids'] ?? [];

                // Sync the holiday codes to the company
                $company->holidayCodes()->sync($holiday_code_ids);

                // Refresh the company model to ensure it reflects the updated holiday codes
                $company->refresh();

            DB::commit();
            } catch (Exception $e) {
                DB::rollback();
                error_log($e->getMessage());
                throw $e;
            }
    }   
}
