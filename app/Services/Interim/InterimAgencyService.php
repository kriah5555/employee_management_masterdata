<?php

namespace App\Services\Interim;

use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Services\AddressService;
use App\Services\BaseService;
use App\Services\Sector\SectorService;
use App\Models\Interim\InterimAgency;

class InterimAgencyService extends BaseService
{
    protected $sectorService;

    protected $address_service;

    public function __construct(InterimAgency $interimAgency, SectorService $sectorService)
    {
        parent::__construct($interimAgency);
        $this->address_service = app(AddressService::class);
    }

    public function create($values)
    {
        try {
            DB::beginTransaction();

                $address           = $this->address_service->createNewAddress($values['address']);
                $values['address'] = $address->id;
                $company           = parent::create($values);

            DB::commit();
            return $company;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($interim_agency, $values)
    {
        try {
            DB::beginTransaction();

                $this->address_service->updateAddress($interim_agency->address, $values['address']);
                unset($values['address']);
                $interim_agency->update($values);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate()
    {
        return [];
    }

    public function getOptionsToEdit($company_id)
    {
        $company_details    = $this->get($company_id, ['address']);
        $options            = $this->getOptionsToCreate();
        $options['details'] = $company_details;
        return $options;
    }

    public function getInterimAgencyOptions()
    {
        try {
            return $this->model::where('status', true)->select(['id as value', 'name as label'])->get();
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}