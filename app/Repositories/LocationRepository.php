<?php

namespace App\Repositories;

use App\Interfaces\LocationRepositoryInterface;
use App\Models\Location;

class LocationRepository implements LocationRepositoryInterface
{
    public function getAllLocations()
    {
        return Location::all();
    }

    public function getCompanyLocations(string $companyId)
    {
        return Location::where('company', '=', $companyId)->get();
    }

    public function getLocationById(string $locationId): Location
    {
        return Location::findOrFail($locationId);
    }

    public function deleteLocation(string $locationId)
    {
        Location::destroy($locationId);
    }

    public function createLocation(array $locationDetails): Location
    {
        return Location::create($locationDetails);
    }

    public function updateLocation(string $locationId, array $newDetails)
    {
        return Location::whereId($locationId)->update($newDetails);
    }
}