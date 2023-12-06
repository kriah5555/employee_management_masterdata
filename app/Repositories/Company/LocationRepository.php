<?php

namespace App\Repositories\Company;

use App\Interfaces\Company\LocationRepositoryInterface;
use App\Models\Company\Location;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use App\Repositories\Employee\ResponsiblePersonRepository;

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
        $location_details = Location::with($relations)->findOrFail($locationId);
        $location_details->responsible_person_details = ($location_details->responsible_person_id) ? app(ResponsiblePersonRepository::class)->getResponsiblePersonById($location_details->responsible_person_id, getCompanyId()) : null; # if the responsible person is removed and the user still linked to the location the because of this he wil not be fetched
        return $location_details;
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
