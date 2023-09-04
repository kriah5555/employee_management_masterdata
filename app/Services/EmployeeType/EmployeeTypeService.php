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
            'dimonaTypeConfig',
            'dimonaTypeConfig.dimonaType',
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
        // $employeeType->dimonaTypeConfig->dimonaTypeValue;
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

    public function update(EmployeeType $employee_type, $values)
    {
        try {
            DB::beginTransaction();
            $employee_type->update($values);
            $employee_type->contractTypes()->sync($values['contract_types'] ?? []);
            DB::commit();
            return $employee_type;
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