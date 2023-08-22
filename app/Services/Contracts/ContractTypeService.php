<?php

namespace App\Services\Contracts;

use App\Models\Address;
use App\Models\Contracts\ContractType;
use Illuminate\Support\Facades\DB;

class ContractTypeService
{
    public function getAllContractTypes()
    {
        return ContractType::all();
    }

    public function getCreateContractTypeOptions()
    {
        $data = [];
        $data['renewal_types'] = $this->getContractRenewalOptions();
        return $data;
    }

    public function getContractRenewalOptions()
    {
        return config('constants.CONTRACT_TYPE_RENEWAL_OPTIONS');
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();
            $employee_type = ContractType::create($values);
            DB::commit();
            return $employee_type ;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getContractTypeDetails($id)
    {
        return ContractType::findOrFail($id);
    }
}
