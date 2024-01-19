<?php

namespace App\Services\Company;

use App\Services\Company\LocationService;
use App\Interfaces\Services\Company\CompanyLocationServiceInterface;

class CompanyLocationService implements CompanyLocationServiceInterface
{
    public function __construct(protected LocationService $locationService)
    {
    }

    public function createCompanyLocations($values, $responsible_person_ids = [])
    {
        $location_ids = [];
        if (isset($values['locations'])) {
            foreach ($values['locations'] as $index => $location_details) {
                if (!empty($location_details['responsible_persons'])) {
                    $location_details['responsible_person_id'] = $responsible_person_ids[$location_details['responsible_persons'][0]];
		}
		$location_details['responsible_persons'] = array_map(function ($index) use ($responsible_person_ids) {
                    return $responsible_person_ids[$index];
                }, $location_details['responsible_persons']);
                $location_ids[$index] = $this->locationService->create($location_details)->id;
            }
        }
        return $location_ids;
    }
}
