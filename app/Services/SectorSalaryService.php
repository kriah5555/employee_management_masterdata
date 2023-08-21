<?php

namespace App\Services;

use App\Models\Sector\Sector;
use Illuminate\Support\Facades\DB;
use App\Models\MinimumSalary;
use App\Services\SectorService;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;

class SectorSalaryService
{
    protected $sector_service;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function getMinimumSalariesBySectorId($id, $increment_coefficient = '')
    {
        $sector = $this->sectorService->getSectorDetails($id);
        $sector->salaryConfig->salarySteps;
        $data = [];
        $return = [];
        $return['salaries'] = [];
        foreach ($sector->salaryConfig->salarySteps as $item) {
            $return['levels'] = $sector->salaryConfig->steps;
            $return['categories'] = $sector->salaryConfig->category;
            $data = [];
            $data['level'] = $item->level;
            foreach ($item->minimumSalary as $salary_item) {
                $data['cat'.$salary_item->category_number] = ($increment_coefficient) 
                ? str(($salary_item->salary + ($salary_item->salary * ($increment_coefficient / 100))) )
                : $salary_item->salary;
            }
            $return['salaries'][] = $data;
        }
        return $return;
    }

    // public function updateMinimumSalaries($sectorId, $values)
    // {
    //     $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();
    //     foreach ($values as $value) {
    //         $sectorSalaryStep = SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
    //         ->where('level', $value['level'])->firstOrFail();
    //         foreach ($value['categories'] as $category_value) {
    //             $minimumSalary = MinimumSalary::where('sector_salary_steps_id', $sectorSalaryStep->id)
    //             ->where('category_number', $category_value['category'])->firstOrFail();
    //             $minimumSalary->salary = (float) str_replace(',', '.', $category_value['minimum_salary']);
    //             $minimumSalary->save();
    //         }
    //     }
    // }

    public function updateMinimumSalaries($sectorId, $values)
    {
        $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();

        foreach ($values as $value) {
            $sectorSalaryStep = SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
                                ->where('level', $value['level'])->firstOrFail();

            foreach ($value['categories'] as $category_value) {
                $this->updateMinimumSalary($sectorSalaryStep->id, $category_value['category'], $category_value['minimum_salary']);
            }
        }
    }

    private function updateMinimumSalary($sectorSalaryStepId, $category, $minimumSalary)
    {
        $minimumSalaryModel = MinimumSalary::where('sector_salary_steps_id', $sectorSalaryStepId)
            ->where('category_number', $category)->firstOrFail();

        $minimumSalaryModel->salary = (float) str_replace(',', '.', $minimumSalary);
        $minimumSalaryModel->save();
    }

    public function incrementMinimumSalaries($sectorId, $increment_coefficient)
    {
        $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();

        $sectorSalarySteps = SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
            ->whereIn('level', range(1, $sectorSalaryConfig->steps))
            ->get()->pluck('id');

        # update all the minimum salaries by $increment_coefficient %
        MinimumSalary::whereIn('sector_salary_steps_id', $sectorSalarySteps)
        ->update(['salary' => DB::raw("salary + (salary * $increment_coefficient / 100)")]);
    }

}
