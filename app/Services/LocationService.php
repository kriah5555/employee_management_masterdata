<?php

namespace App\Services;

use App\Models\Workstation;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Repositories\Company\LocationRepository;
use App\Services\BaseService;
use App\Models\Company\Location;

class LocationService extends BaseService
{
    protected $locationRepository;

    protected $addressService;

    public function __construct(LocationRepository $locationRepository, AddressService $addressService)
    {
        $this->locationRepository = $locationRepository;
        $this->addressService = $addressService;
    }

    public function getActiveLocations()
    {
        return $this->locationRepository->getActiveLocations();
    }

    public function getLocations()
    {
        return $this->locationRepository->getLocations();
    }
    public function getLocationById($locationId)
    {
        return $this->locationRepository->getLocationById($locationId);
    }
    public function deleteLocation($locationId)
    {
        return $this->locationRepository->deleteLocation($locationId);
    }

    public static function getLocationRules($for_company_creation = true)
    {
        $location_rules = [
            'location_name' => 'required|string|max:255',
            'address'       => ['required', new AddressRule()],
        ];

        if (!$for_company_creation) { # in company creation flow multi step form the workstation and locations are newly created and added so this ocndition will not be required
            $location_rules = self::addLocationCreationRules($location_rules);
        }
        return $location_rules;
    }

    public static function addLocationCreationRules($rules)
    {
        $rules['status'] = 'required|boolean';
        $rules['company'] = [
            'bail',
            'required',
            'integer',
            Rule::exists('companies', 'id')
        ];
        return $rules;
    }

    public function create($values)
    {
        try {
            // setTenantDB('');
            DB::beginTransaction();
            $address = $this->addressService->createNewAddress($values['address']);
            $values['address'] = $address->id;
            $location = $this->locationRepository->createLocation($values);
            DB::commit();
            return $location;
        } catch (\Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($location, $values)
    {
        try {
            DB::beginTransaction();
            $this->addressService->updateAddress($location->address, $values['address']);
            unset($values['address']);
            unset($values['company']);
            $location->update($values);
            DB::commit();
            return $location;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate($company_id)
    {
        return ['workstations' => Workstation::where('status', true)->where('company', $company_id)->get(['id as value', 'workstation_name as label'])];
    }

    public function getOptionsToEdit($location_id)
    {
        $location_details = $this->get($location_id, ['address']);
        $options = $this->getOptionsToCreate($location_details->company);
        $options['details'] = $location_details;
        return $options;
    }
}
