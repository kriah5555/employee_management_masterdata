<?php

namespace App\Services\EmployeeType;

use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\Dimona\DimonaType;
use App\Services\Contract\ContractTypeService;

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
        $options['employee_categories'] = $this->getEmployeeCategoryOptions();
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
        $options = [];
        $contract_types = EmployeeTypeCategory::where('status', '=', true)->get();
        foreach ($contract_types as $value) {
            $options[] = [
                'value' => $value['id'],
                'label' => $value['name'],
            ];
        }
        return $options;
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
