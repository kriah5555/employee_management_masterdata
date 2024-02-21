<?php

namespace App\Services\Company\Contract;

use App\Models\Company\Contract\ContractConfiguration;
use Illuminate\Support\Facades\DB;
use App\Services\Company\LocationService;
use App\Services\EmployeeType\EmployeeTypeService;
use Exception;

class ContractConfigurationService
{
    public function __construct(protected LocationService $location_service, protected EmployeeTypeService $employee_type_service)
    {
    }

    public function getContractConfigurations()
    {
        try {
            $company_id = request()->header('Company-Id');
            $employee_types = collect($this->employee_type_service->getCompanyEmployeeTypes($company_id)); # converting array to collection

            $employee_types = $employee_types->sortBy('name'); // Sort employee types by name
            $locations = $this->location_service->getLocations();
            $locationArray = [];
            $contract_config = ContractConfiguration;

            $locations->each(function ($location) use (&$locationArray, $employee_types, $contract_config) {
                $location_id = $location->id;

                $locationArray[$location_id] = [
                    'location_id'                   => $location_id,
                    'location_name'                 => $location->location_name,
                    'employee_type_contract_status' => [],
                ];

                $locationArray[$location_id]['employee_type_contract_status'] = $employee_types->map(function ($employee_type) use ($location_id, $contract_config) {
                    $contractConfig = $contract_config::where(['employee_type_id' => $employee_type['id'], 'location_id' => $location_id])->first();

                    return [
                        'employee_type'    => $employee_type['name'],
                        'employee_type_id' => $employee_type['id'],
                        'status'           => $contractConfig ? $contractConfig->status : false,
                    ];
                })->all();
            });

            return array_values($locationArray);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateContractConfigurations($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();

            $company_id = request()->header('Company-Id');
            $employee_types = collect($this->employee_type_service->getCompanyEmployeeTypes($company_id));
            $validEmployeeTypeIds = $employee_types->pluck('id')->toArray();
            ContractConfiguration::whereNotIn('employee_type_id', $validEmployeeTypeIds)->delete();

            $locationsData = $values['locations'];
            foreach ($locationsData as $locationData) {
                $location_id = $locationData['location_id'];
                $employeeTypeContractStatus = $locationData['employee_type_contract_status'];

                foreach ($employeeTypeContractStatus as $employeeTypeStatus) {
                    $employee_type_id = $employeeTypeStatus['employee_type_id'];
                    $status = $employeeTypeStatus['status'];

                    // Use updateOrInsert to either update an existing record or insert a new one
                    ContractConfiguration::updateOrInsert(
                        [
                            'employee_type_id' => $employee_type_id,
                            'location_id'      => $location_id
                        ],
                        [
                            'status' => $status,
                        ]
                    );
                }
            }
            DB::connection('tenant')->commit();
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
