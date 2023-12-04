<?php

namespace App\Services\Company;

use App\Interfaces\Services\Company\CompanyWorkstationServiceInterface;
use App\Services\WorkstationService;

class CompanyWorkstationService implements CompanyWorkstationServiceInterface
{
    protected $workstationService;
    public function __construct(WorkstationService $workstationService, )
    {
        $this->workstationService = $workstationService;
    }

    public function createCompanyWorkstations($values, $location_ids, $company_id)
    {
        if (!empty($location_ids) && isset($values['workstations'])) {
            foreach ($values['workstations'] as $workstation) {
                $workstation['locations'] = array_map(function ($value) use ($location_ids) {
                    return $location_ids[$value];
                }, $workstation['locations_index']);
                $workstation['company'] = $company_id;
                $this->workstationService->create($workstation);
            }
        }
    }
}
