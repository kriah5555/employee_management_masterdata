<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeType\EmployeeTypeContract;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\Contracts\ContractType;
use App\Models\Dimona\DimonaType;

class EmployeeTypeService
{
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
        $options['contract_types'] = $this->getContractTypesOptions();
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
        $options['details'] = $this->show($id);
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
    public function getContractTypesOptions()
    {
        $options = [];
        $contract_types = ContractType::where('status', '=', true)->get();
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
        $options = [];
        $contract_types = DimonaType::where('status', '=', true)->get();
        foreach ($contract_types as $value) {
            $options[] = [
                'value' => $value['id'],
                'label' => $value['name'],
            ];
        }
        return $options;
    }

    public function getEmployeeTypeOptions()
    {
        $options = [];
        $employee_types = EmployeeType::where('status', '=', true)->get();
        foreach ($employee_types as $value) {
            $options[] = [
                'value' => $value['id'],
                'label' => $value['name'],
            ];
        }
        return $options;
    }
}
