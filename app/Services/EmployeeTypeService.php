<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeType\EmployeeTypeContract;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\Contracts\ContractTypes;
use App\Models\Contracts\ContractRenewal;
use App\Models\Dimona\DimonaType;

class EmployeeTypeService
{
    public function getEmployeeTypeDetails($id)
    {
        return EmployeeType::findOrFail($id);
    }

    public function getAllEmployeeTypes()
    {
        return EmployeeType::all();
    }

    public function getActiveEmployeeTypes()
    {
        return EmployeeType::where('status', '=', true)->get();
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();
            $employee_type = EmployeeType::create($values);
            self::craeteOrUpdateEmployeeTypeContract($values, $employee_type->id);
            DB::commit();
            return $employee_type ;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update(EmployeeType $employee_type, $values)
    {
        try {
            DB::beginTransaction();
            EmployeeType::update($values);
            self::craeteOrUpdateEmployeeTypeContract($values, $employee_type->id);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function craeteOrUpdateEmployeeTypeContract($values, $employee_type_id)
    {
        EmployeeTypeContract::where('employee_type_id', $employee_type_id)
            ->update(['status' => 0]);

        return EmployeeTypeContract::updateOrCreate(
            [
                'employee_type_id'    => $employee_type_id,
                'contract_type_id'    => $values['contract_type_id'],
                'contract_renewal_id' => $values['contract_renewal_id'],
            ], # conditions
            [
            'employee_type_id'    => $employee_type_id,
            'contract_type_id'    => $values['contract_type_id'],
            'contract_renewal_id' => $values['contract_renewal_id'],
            'status'              => 1
            ]
        );
    }

    public function getCreateEmployeeTypeOptions()
    {
        $options['employee_categories'] = $this->getEmployeeCategoryOptions();
        $options['contract_types'] = $this->getContractTypesOptions();
        $options['dimona_types'] = $this->getDimonaTypesOptions();
        return $options;
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
        $contract_types = ContractTypes::where('status', '=', true)->get();
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
}
