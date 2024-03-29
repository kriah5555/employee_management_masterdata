<?php

namespace App\Services\Company;

use App\Models\Company\Workstation;
use App\Services\Company\AddressService;
use App\Rules\AddressRule;
use Illuminate\Support\Facades\DB;
use App\Repositories\Company\LocationRepository;
use App\Services\Employee\ResponsiblePersonService;

class LocationService
{

    public function __construct(protected LocationRepository $locationRepository, protected AddressService $addressService)
    {
    }

    public function getActiveLocations()
    {
        return $this->locationRepository->getActiveLocations();
    }
    public function getActiveLocationsOptions()
    {
        $employeeTypes = $this->getActiveLocations();
        return $employeeTypes->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->location_name,
            ];
        })->toArray();
    }

    public function getLocations()
    {
        return $this->locationRepository->getLocations();
    }

    public function getLocationById($locationId)
    {
        return $this->locationRepository->getLocationById($locationId, ['address', 'responsiblePersons']);
    }

    public function deleteLocation($locationId)
    {
        return $this->locationRepository->deleteLocation($locationId);
    }

    public function getLocationWorkstations($locationId)
    {
        return $this->locationRepository->getLocationWorkstations($locationId);
    }

    public static function getLocationRules($for_company_creation = true)
    {
        $location_rules = [
            'location_name' => 'required|string|max:255',
            'address'       => ['required', new AddressRule()],
            'status'        => $for_company_creation ? 'required|boolean' : '',
        ];
        return $location_rules;
    }

    public function create($values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $address = $this->addressService->createNewAddress($values['address']);
            $values['address'] = $address->id;
            $location = $this->locationRepository->createLocation($values);
            $location->responsiblePersons()->sync($values['responsible_persons'] ?? []);
            DB::connection('tenant')->commit();
            return $location;
        } catch (\Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function update($location_id, $values)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $location = self::getLocationById($location_id);
            $this->addressService->updateAddress($location->address, $values['address']);
            unset($values['address']);
            unset($values['company']);
            $responsiblePersons = $values['responsible_persons'] ?? [];
            unset($values['responsible_persons']);
            $location->update([
                'location_name' => $values['location_name'],
                'status'        => $values['status']
            ]);
            $location->responsiblePersons()->sync($responsiblePersons ?? []);
            DB::connection('tenant')->commit();
            return $location;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getOptionsToCreate()
    {
        return [
            'workstations'        => Workstation::where('status', true)->get(),
            'responsible_persons' => app(ResponsiblePersonService::class)->getCompanyResponsiblePersonOptions(getCompanyId()),
        ];
    }
    public function getLocationsList()
    {
        $locations = $this->locationRepository->getActiveLocations();
        $locationsList = [];
        foreach ($locations as $location) {
            $locationsList[] = [
                'value' => $location->id,
                'label' => $location->location_name
            ];
        }
        return $locationsList;
    }
}
