<?php

namespace App\Services\Contract;

use App\Models\Contract\ContractType;
use Illuminate\Support\Facades\DB;
use App\Models\Contract\ContractRenewalType;
use Exception;

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
        return ContractRenewalType::where('status', '=', true)->select(['id as value', 'name as label'])->get();
    }

    public function store($values)
    {
        try {
            DB::beginTransaction();
            $employee_type = ContractType::create($values);
            DB::commit();
            return $employee_type;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function show($id)
    {
        return ContractType::with(['contractRenewalType'])->findOrFail($id);
    }

    /**
     * Function to get all the options required to edit contract type
     */
    public function edit($id)
    {
        $options = $this->create();
        $contractType = ContractType::findOrFail($id);
        $contractType->contractRenewalTypeValue;
        $options['details'] = $contractType;
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
    public function getContractTypesOptions()
    {
        return ContractType::where('status', '=', true)
            ->select('id as value', 'name as label')
            ->get();
    }
}