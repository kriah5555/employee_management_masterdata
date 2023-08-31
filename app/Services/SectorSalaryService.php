<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use App\Models\MinimumSalary;
use App\Models\MinimumSalaryBackup;
use App\Services\SectorService;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;

class SectorSalaryService
{
    protected $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function getMinimumSalariesBySectorId($id)
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
                $data['cat' . $salary_item->category_number] = !$salary_item->salary ? $salary_item->salary : number_format($salary_item->salary, 4);
            }
            $return['salaries'][] = $data;
        }
        return $return;
    }

    public function updateMinimumSalaries($sectorId, $values)
    {
        try {
            DB::beginTransaction();
            $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();

            #backup the old data
            $sectorSalarySteps_ids = $this->getSalaryStepidsBySectorSalaryConfig($sectorSalaryConfig);
            $this->addSalaryBackup($sectorSalarySteps_ids, $sectorSalaryConfig);

            foreach ($values as $value) {
                $sectorSalaryStep = SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
                    ->where('level', $value['level'])->firstOrFail();

                foreach ($value['categories'] as $category_value) {
                    $this->updateMinimumSalary($sectorSalaryStep->id, $category_value['category'], $category_value['minimum_salary']);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function updateMinimumSalary($sectorSalaryStepId, $category, $minimumSalary)
    {
        $minimumSalaryModel = MinimumSalary::where('sector_salary_steps_id', $sectorSalaryStepId)
            ->where('category_number', $category)->firstOrFail();

        $minimumSalaryModel->salary = (float) str_replace(',', '.', $minimumSalary);
        $minimumSalaryModel->save();
    }

    // public function incrementMinimumSalaries($sectorId, $increment_coefficient)
    // {
    //     try {
    //         DB::beginTransaction();
    //         $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();

    //         $sectorSalarySteps = SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
    //             ->whereIn('level', range(1, $sectorSalaryConfig->steps))
    //             ->get()->pluck('id');

    //         $old_salary_data = MinimumSalary::whereIn('sector_salary_steps_id', $sectorSalarySteps)->get();
    //         $save_old_data = [];
    //         foreach ($old_salary_data as $salary_data) {
    //             $save_old_data[$salary_data->sector_salary_steps_id][$salary_data->category_number] = $salary_data->salary;
    //         }

    //         # add to backup table
    //         MinimumSalaryBackup::create([
    //             'sector_salary_config_id' => $sectorSalaryConfig->id,
    //             'category'                => $sectorSalaryConfig->category,
    //             'salary_data'             => $save_old_data
    //         ]);

    //         # update all the minimum salaries by $increment_coefficient %
    //         MinimumSalary::whereIn('sector_salary_steps_id', $sectorSalarySteps)
    //             ->update(['salary' => DB::raw("salary + (salary * $increment_coefficient / 100)")]);
    //         DB::commit();
    //     } catch (Exception $e) {
    //         DB::rollback();
    //         error_log($e->getMessage());
    //         throw $e;
    //     }
    // }


    private function getSalaryStepidsBySectorSalaryConfig(SectorSalaryConfig $sectorSalaryConfig) 
    {
        return SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
                ->whereIn('level', range(1, $sectorSalaryConfig->steps))
                ->get()->pluck('id');
    }

    public function incrementMinimumSalaries($sectorId, $increment_coefficient)
    {
        try {
            DB::beginTransaction();
            $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();

            #backup the old data
            $sectorSalarySteps_ids = $this->getSalaryStepidsBySectorSalaryConfig($sectorSalaryConfig);
            $this->addSalaryBackup($sectorSalarySteps_ids, $sectorSalaryConfig);

            # update all the minimum salaries by $increment_coefficient %
            MinimumSalary::whereIn('sector_salary_steps_id', $sectorSalarySteps_ids)
                ->update(['salary' => DB::raw("salary + (salary * $increment_coefficient / 100)")]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function addSalaryBackup($sectorSalarySteps, $sectorSalaryConfig)
    {
        $old_salary_data = MinimumSalary::whereIn('sector_salary_steps_id', $sectorSalarySteps)->get();
        $save_old_data = [];
        
        foreach ($old_salary_data as $salary_data) {
            $save_old_data[$salary_data->sector_salary_steps_id][$salary_data->category_number] = $salary_data->salary;
        }

        MinimumSalaryBackup::create([
            'sector_salary_config_id' => $sectorSalaryConfig->id,
            'category'                => $sectorSalaryConfig->category,
            'salary_data'             => $save_old_data
        ]);

        return $old_salary_data;
    }

    public function undoIncrementedMinimumSalaries($sectorId)
    {
        try {
            DB::beginTransaction();

            $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();
            $revertSalaryData = $this->getRevertSalaryData($sectorSalaryConfig);
            $revertSalaryDataWithCat = $this->getRevertSalaryDataWithCategory($sectorSalaryConfig);

            if ($revertSalaryDataWithCat) {
                $this->applyRevertData($revertSalaryDataWithCat);
            } elseif ($revertSalaryData->isNotEmpty()) {
                $revertCategory = $revertSalaryData->first()->category;
                return $this->getCategoryMismatchMessage($sectorSalaryConfig, $revertCategory);
            } elseif ($revertSalaryData->isEmpty()) {
                return "Nothing to revert";
            } else {
                return 'success';
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function getRevertSalaryData($sectorSalaryConfig)
    {
        return MinimumSalaryBackup::where('sector_salary_config_id', $sectorSalaryConfig->id)
            ->orderBy('revert_count', 'desc')
            ->get();
    }

    private function getRevertSalaryDataWithCategory($sectorSalaryConfig)
    {
        return MinimumSalaryBackup::where('sector_salary_config_id', $sectorSalaryConfig->id)
            ->where('category', $sectorSalaryConfig->category)
            ->orderBy('revert_count', 'desc')
            ->first();
    }

    private function applyRevertData($revertSalaryDataWithCat)
    {
        foreach ($revertSalaryDataWithCat->salary_data as $sectorSalaryStepsId => $salaryData) {
            foreach ($salaryData as $category => $salary) {
                MinimumSalary::where('sector_salary_steps_id', $sectorSalaryStepsId)
                    ->where('category_number', $category)
                    ->update(['salary' => $salary]);
            }
        }
        $revertSalaryDataWithCat->delete();
    }

    private function getCategoryMismatchMessage($sectorSalaryConfig, $revertCategory)
    {
        return "The category in the current sector ({$sectorSalaryConfig->category}) is mismatching with the category ($revertCategory) in the revert data";
    }

}