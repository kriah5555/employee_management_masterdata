<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Models\EmployeeType\EmployeeTypeContract;

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
            $employee_type = EmployeeType::update($values);
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
}
