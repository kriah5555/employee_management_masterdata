<?php

namespace App\Services;

use App\Models\Locations;
use App\Services\AddressService;

class LocationService
{
    public function getLocationDetails($id)
    {
        return Locations::findOrFail($id);
    }

    public function createNewLocations($values)
    {
        try {
            $address  = new AddressService();
            $address  = $address->createNewAddress($values['address']);
            $values['address'] = $address->id;
            $location = Locations::create($values);
            return $location ;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
