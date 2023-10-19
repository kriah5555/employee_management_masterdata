<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\LocationRepositoryInterface;
use App\Models\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class LocationRepository implements LocationRepositoryInterface
{
    public function getLocationsOfCompany($companyId)
    {
        return Location::where('company', '=', $companyId)->get();
    }
    public function getActiveLocationsOfCompany($companyId)
    {
        return Location::where('company', '=', $companyId)->where('status', '=', true)->get();
    }

    public function getLocationById(string $locationId, array $relations = []): Collection|Builder|Location
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
}