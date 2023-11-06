<?php

namespace App\Interfaces\Company;

interface LocationRepositoryInterface
{
    public function getLocations();

    public function getActiveLocations();

    public function getLocationById(string $locationId);

    public function deleteLocation(string $locationId);

    public function createLocation(array $details);

    public function updateLocation(string $locationId, array $updatedDetails);
}
