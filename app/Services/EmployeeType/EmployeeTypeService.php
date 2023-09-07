<?php

namespace App\Services\EmployeeType;

use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\EmployeeType\EmployeeTypeConfig;
use App\Models\EmployeeType\EmployeeTypeDimonaConfig;
use App\Models\Dimona\DimonaType;
use App\Services\Contract\ContractTypeService;
use Exception;

class EmployeeTypeService
{
    protected $contractTypeService;

    public function __construct(ContractTypeService $contractTypeService)
    {
        $this->contractTypeService = $contractTypeService;
    }
    /**
     * Function to get all the employee types
     */
    public function index()
    {
        return $this->getAllEmployeeTypes();
    }

    /**
     * Function to get all the options required to create employee type
     */
    public function create()
    {
        $options = [];
        $options['employee_type_categories'] = $this->getEmployeeCategoryOptions();
        $options['contract_types'] = $this->contractTypeService->getContractTypesOptions();
        $options['dimona_types'] = $this->getDimonaTypesOptions();
        return $options;
    }

    /**
     * Function to save a new employee type
     */
    public function store($values)
    {
        try {
            return DB::transaction(function () use ($values) {
                $employee_type = EmployeeType::create($values);
                $employee_type->contractTypes()->sync($values['contract_types'] ?? []);
                $values['employee_type_id'] = $employee_type->id;
                EmployeeTypeConfig::create($values);
                EmployeeTypeDimonaConfig::create($values);
                return $employee_type;
            });
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    /**
     * Function to get employee type details for API
     */
    public function show($id)
    {
        return EmployeeType::with([
            'employeeTypeCategory',
            'contractTypes',
            'dimonaConfig',
            'dimonaConfig.dimonaType',
        ])->findOrFail($id);
    }


    /**
     * Function to get all the options required to edit employee type
     */
    public function edit($id)
    {
        $options = $this->create();
        $employeeType = EmployeeType::findOrFail($id);
        $employeeType->employeeTypeCategoryValue;
        $employeeType->contractTypesValue;
        $employeeType->employeeTypeConfig;
        $employeeType->dimonaConfig;
        $employeeType->dimonaConfig->dimonaTypeValue;
        $options['details'] = $employeeType;
        return $options;
    }

    /**
     * Function to get the employee type details by id
     */
    public function getEmployeeTypeDetails($id)
    {
        return EmployeeType::findOrFail($id);
    }

    /**
     * Function to get retrieve all the employee types from the database
     */
    public function getAllEmployeeTypes()
    {
        return EmployeeType::all();
    }

    /**
     * Function to get retrieve only active employee types from the database
     */
    public function getActiveEmployeeTypes()
    {
        return EmployeeType::where('status', '=', true)->get();
    }

    public function update(EmployeeType $employeeType, $values)
    {
        try {
            DB::beginTransaction();
            $employeeType->update($values);
            $employeeType->contractTypes()->sync($values['contract_types'] ?? []);
            $employeeTypeConfig = $employeeType->employeeTypeConfig;
            $employeeTypeConfig->update($values);
            $dimonaConfig = $employeeType->dimonaConfig;
            $dimonaConfig->update($values);
            DB::commit();
            return $employeeType;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeCategoryOptions()
    {
        return EmployeeTypeCategory::where('status', '=', true)
            ->select('id as value', 'name as label')
            ->get();
    }
    public function getDimonaTypesOptions()
    {
        return DimonaType::where('status', '=', true)
            ->select('id as value', 'name as label')
            ->get();
    }

    public function getEmployeeTypeOptions()
    {
        return EmployeeType::where('status', '=', true)
            ->select('id as value', 'name as label')
            ->get();
    }
}