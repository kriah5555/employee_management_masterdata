<?php

namespace App\Services\Contracts;

use App\Models\Contracts\ContractType;
use Illuminate\Support\Facades\DB;

class ContractTypeService
{
    public function index()
    {
        return ContractType::all();
    }

    public function create()
    {
        $data = [];
        $data['renewal_types'] = $this->getContractRenewalOptions();
        return $data;
    }

    public function getContractRenewalOptions()
    {
        return config('constants.CONTRACT_TYPE_RENEWAL_OPTIONS');
    }

    public function store($values)
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

    public function show($id)
    {
        return ContractType::findOrFail($id);
    }

    /**
     * Function to get all the options required to edit contract type
     */
    public function edit($id)
    {
        $options = $this->create();
        $options['details'] = $this->show($id);
        return $options;
    }

    public function update(ContractType $contractType, $values)
    {
        try {
            DB::beginTransaction();
            $contractType->update($values);
            DB::commit();
            return $contractType;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
