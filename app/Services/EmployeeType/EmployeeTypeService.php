<?php

namespace App\Services\EmployeeType;

use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Repositories\EmployeeType\EmployeeTypeRepository;
use App\Repositories\EmployeeType\EmployeeTypeConfigRepository;
use App\Repositories\EmployeeType\EmployeeTypeDimonaConfigRepository;
use App\Repositories\EmployeeType\EmployeeTypeCategoryRepository;

class EmployeeTypeService
{
    protected $employeeTypeRepository;

    protected $employeeTypeConfigRepository;

    protected $employeeTypeDimonaConfigRepository;

    protected $employeeTypeCategoryRepository;

    public function __construct(
        EmployeeTypeRepository $employeeTypeRepository,
        EmployeeTypeConfigRepository $employeeTypeConfigRepository,
        EmployeeTypeDimonaConfigRepository $employeeTypeDimonaConfigRepository,
        EmployeeTypeCategoryRepository $employeeTypeCategoryRepository
    ) {
        $this->employeeTypeRepository = $employeeTypeRepository;
        $this->employeeTypeConfigRepository = $employeeTypeConfigRepository;
        $this->employeeTypeDimonaConfigRepository = $employeeTypeDimonaConfigRepository;
        $this->employeeTypeCategoryRepository = $employeeTypeCategoryRepository;
    }
    /**
     * Function to get all the employee types
     */
    public function getEmployeeTypes()
    {
        return $this->employeeTypeRepository->getEmployeeTypes();
    }

    public function getEmployeeTypesOptions()
    {
        $employeeTypes = $this->getEmployeeTypes();
        return $employeeTypes->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
                // Add more fields as needed
            ];
        })->toArray();
    }

    /**
     * Function to save a new employee type
     */
    public function createEmployeeType($values)
    {
        return DB::transaction(function () use ($values) {
            $employee_type = $this->employeeTypeRepository->createEmployeeType($values);
            $this->employeeTypeRepository->updateEmployeeTypeContractTypes($employee_type, $values['contract_types']);
            $values['employee_type_id'] = $employee_type->id;
            $this->employeeTypeConfigRepository->createEmployeeTypeConfig($values);
            $this->employeeTypeDimonaConfigRepository->createEmployeeTypeDimonaConfig($values);
            return $employee_type;
        });
    }

    /**
     * Function to get employee type details for API
     */
    public function getEmployeeTypeDetails($id)
    {
        $employeeType = $this->employeeTypeRepository->getEmployeeTypeById($id, [
            'employeeTypeCategory',
            'contractTypes',
            'employeeTypeConfig',
            'dimonaConfig',
            'dimonaConfig.dimonaType',
        ]);
        $employeeType->salary_type = [
            'value' => $employeeType->salary_type,
            'label' => config('constants.SALARY_TYPES')[$employeeType->salary_type] ?? '',
        ];
        return $employeeType;
    }

    /**
     * Function to get retrieve all the employee types from the database
     */
    public function getActiveEmployeeTypes()
    {
        return $this->employeeTypeRepository->getActiveEmployeeTypes();
    }

    public function update(EmployeeType $employeeType, $values)
    {
        return DB::transaction(function () use ($employeeType, $values) {
            $this->employeeTypeRepository->updateEmployeeType($employeeType, $values);
            $this->employeeTypeRepository->updateEmployeeTypeContractTypes($employeeType, $values['contract_types']);
            $this->employeeTypeConfigRepository->updateEmployeeTypeConfig($employeeType->employeeTypeConfig, $values);
            $this->employeeTypeDimonaConfigRepository->updateEmployeeTypeDimonaConfig($employeeType->dimonaConfig, $values);
            return $employeeType;
        });
    }

    public function getEmployeeTypeCategories()
    {
        return $this->employeeTypeCategoryRepository->getActiveEmployeeTypeCategories();
    }

    public function getEmployeeTypeOptions()
    {
        return EmployeeType::where('status', '=', true)
            ->select('id as value', 'name as label')
            ->get();
    }

    public function getSalaryTypeOptions()
    {
        $options = config('constants.SALARY_TYPES');

        return array_map(function ($key, $value) {
            return ['value' => $key, 'label' => $value];
        }, array_keys($options), $options);
    }

    public function getEmployeeCategoryOptions()
    {
        return getValueLabelOptionsFromConfig('absence.HOLIDAY_EMPLOYEE_CATEGORY_OPTIONS');
    }

    public function getEmployeeContractTypeOptions()
    {
        return getValueLabelOptionsFromConfig('absence.HOLIDAY_CONTRACT_TYPE_OPTIONS');
    }

    public function getCompanyEmployeeTypes($company_id)
    {
        return $this->employeeTypeRepository->getCompanyEmployeeTypes($company_id);
    }

}
