<?php

namespace App\Services\Company;

use App\Models\Company\Company;
use App\Services\Company\LocationService;
use App\Interfaces\Services\Company\CompanyLocationServiceInterface;

class CompanyLocationService implements CompanyLocationServiceInterface
{
    public function __construct(protected LocationService $locationService)
    {
    }

    public function createCompanyLocations(Company $company, $values)
    {
        $location_ids = [];
        if (isset($values['locations'])) {
            foreach ($values['locations'] as $index => $location_details) {
                $location['company'] = $company->id;
                $location_ids[$index] = $this->locationService->create($location_details)->id;
            }
        }
        return $location_ids;
    }
}
