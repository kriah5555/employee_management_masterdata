<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\LocationRepositoryInterface;
use App\Models\Company\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository implements LocationRepositoryInterface
{
    public function getLocations()
    {
        return Location::all();
    }
    public function getActiveLocations()
    {
        return Location::allActive();
    }

    public function getLocationById(string $locationId, array $relations = ['address']): Collection|Builder|Location
    {
        return Location::with($relations)->findOrFail($locationId);
    }

    public function deleteLocation(string $locationId)
    {
        Location::destroy($locationId);
    }

    public function createLocation(array $details): Location
    {
        return Location::create($details);
    }

    public function updateLocation(string $locationId, array $updatedDetails)
    {
        return Location::whereId($locationId)->update($updatedDetails);
    }

    public function getLocationWorkstations(string $locationId)
    {
        return Location::findOrFail($locationId)->workstations;
    }
}
