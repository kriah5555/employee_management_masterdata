<?php

namespace App\Services;

use App\Models\Sector\Sector;
use Illuminate\Support\Facades\DB;
use App\Models\MinimumSalary;
use App\Services\SectorService;

class SectorSalaryService
{
    protected $sector_service;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function getMinimumSalariesBySectorId($id)
    {
        $sector = $this->sectorService->getSectorById($id);
        // $sector = $this->sectorService->getSectorDetails($id);
        $sector->salaryConfig->salarySteps;
        foreach ($sector->salaryConfig->salarySteps as $item) {
            // print_r($item);exit;
            print_r($item->minimumSalary);exit;
        }
        print_r($sector->salaryConfig->salarySteps);exit;
        $sector->salaryConfig->salarySteps->minimumSalary;
        // $this->getMinimumSalariesBySector($sector);
        return $sector;
        // return MinimumSalary::all();
    }

    public function getMinimumSalariesBySector(Sector $sector)
    {
        $salary_config = $sector->salaryConfig;
        $categories = $salary_config->category;
        $steps = $salary_config->steps;
        print_r([$categories, $steps]);exit;
    }
}
