<?php

namespace App\Interfaces;

interface LocationRepositoryInterface
{
    public function getAllLocations();

    public function getCompanyLocations(string $companyId);

    public function getLocationById(string $locationId);

    public function deleteLocation(string $locationId);

    public function createLocation(array $locationDetails);

    public function updateLocation(string $locationId, array $newDetails);
}