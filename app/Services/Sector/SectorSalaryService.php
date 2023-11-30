<?php

namespace App\Services\Sector;

use Illuminate\Support\Facades\DB;
use App\Models\MinimumSalary;
use App\Models\MinimumSalaryBackup;
use App\Services\Sector\SectorService;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;

class SectorSalaryService
{
    protected $sectorService;

    public function __construct(SectorService $sectorService)
    {
        $this->sectorService = $sectorService;
    }

    public function getMinimumSalariesBySectorId($sector_id, $salary_type = '')
    {
        $field  = $this->getFieldBySalaryType($salary_type);
        $sector = $this->sectorService->getSectorDetails($sector_id);
        $sector->load('salaryConfig.salarySteps.minimumSalary');

        $return = [
            'salaries'   => [],
            'levels'     => $sector->salaryConfig->steps,
            'categories' => $sector->salaryConfig->category,
        ];

        foreach ($sector->salaryConfig->salarySteps as $item) {
            $data = [
                'level' => $item->level,
            ];

            foreach ($item->minimumSalary as $salary_item) {
                $data['cat' . $salary_item->category_number] = formatToEuropeCurrency($salary_item->$field);
            }

            $return['salaries'][] = $data;
        }

        return $return;
    }

    private function getFieldBySalaryType($salary_type)
    {
        switch ($salary_type) {
            case config('constants.MONTHLY_SALARY'):
                return 'monthly_minimum_salary';
            case config('constants.HOURLY_SALARY'):
                return 'hourly_minimum_salary';
            default:
                return ''; 
        }
    }

    public function updateMinimumSalaries($sectorId, $values, $salary_type = '')
    {
        try {
            DB::beginTransaction();
            $field              = $this->getFieldBySalaryType($salary_type);
            $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();

            #backup the old data
            $sectorSalarySteps_ids = $this->getSalaryStepIdsBySectorSalaryConfig($sectorSalaryConfig);
            $this->addSalaryBackup($sectorSalarySteps_ids, $sectorSalaryConfig, $salary_type);

            foreach ($values as $value) {
                $sectorSalaryStep = SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
                    ->where('level', $value['level'])->firstOrFail();
                foreach ($value['categories'] as $category_value) {
                    $this->updateMinimumSalary($sectorSalaryStep->id, $category_value['category'], $category_value['minimum_salary'], $field);
                }
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function updateMinimumSalary($sectorSalaryStepId, $category, $minimumSalary, $field)
    {
        $minimumSalaryModel = MinimumSalary::where('sector_salary_steps_id', $sectorSalaryStepId)
            ->where('category_number', $category)->firstOrFail();

        $minimumSalaryModel->$field = $minimumSalary;
        $minimumSalaryModel->save();
    }

    private function getSalaryStepIdsBySectorSalaryConfig(SectorSalaryConfig $sectorSalaryConfig) 
    {
        return SectorSalarySteps::where('sector_salary_config_id', $sectorSalaryConfig->id)
                ->whereIn('level', range(0, $sectorSalaryConfig->steps))
                ->get()->pluck('id');
    }

    public function incrementMinimumSalaries($sectorId, $increment_coefficient)
    {
        try {
            DB::beginTransaction();
            $sectorSalaryConfig = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();

            #backup the old data
            $sectorSalarySteps_ids = $this->getSalaryStepIdsBySectorSalaryConfig($sectorSalaryConfig);
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

    private function addSalaryBackup($sectorSalarySteps, $sectorSalaryConfig, $salary_type)
    {
        $field           = $this->getFieldBySalaryType($salary_type);
        $old_salary_data = MinimumSalary::whereIn('sector_salary_steps_id', $sectorSalarySteps)->get();
        $save_old_data   = [];
        foreach ($old_salary_data as $salary_data) {
            $save_old_data[$salary_data->sector_salary_steps_id][$salary_data->category_number] = $salary_data->$field;
        }

        MinimumSalaryBackup::create([
            'sector_salary_config_id' => $sectorSalaryConfig->id,
            'category'                => $sectorSalaryConfig->category,
            'salary_type'             => $salary_type,
            'salary_data'             => $save_old_data
        ]);

        return $old_salary_data;
    }

    public function undoIncrementedMinimumSalaries($sectorId, $salary_type)
    {
        try {
            DB::beginTransaction();

            $sectorSalaryConfig      = SectorSalaryConfig::where('sector_id', $sectorId)->firstOrFail();
            $revertSalaryData        = $this->getRevertSalaryData($sectorSalaryConfig, $salary_type); # to validate if the category are mismatching or not
            $revertSalaryDataWithCat = $this->getRevertSalaryDataWithCategory($sectorSalaryConfig, $salary_type);

            if ($revertSalaryDataWithCat) {
                $this->applyRevertData($revertSalaryDataWithCat, $salary_type);
            } elseif ($revertSalaryData->isNotEmpty()) {
                $revertCategory = $revertSalaryData->first()->category;
                return $this->getCategoryMismatchMessage($sectorSalaryConfig, $revertCategory);
            } elseif ($revertSalaryData->isEmpty()) {
                return "Nothing to revert";
            } else {
                return 'Success';
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function getRevertSalaryData($sectorSalaryConfig, $salary_type)
    {
        return MinimumSalaryBackup::where('sector_salary_config_id', $sectorSalaryConfig->id)
            ->where('salary_type', $salary_type)
            ->orderByDesc('revert_count')
            ->get();
    }

    private function getRevertSalaryDataWithCategory($sectorSalaryConfig, $salary_type)
    {
        return MinimumSalaryBackup::where('sector_salary_config_id', $sectorSalaryConfig->id)
            ->where('category', $sectorSalaryConfig->category)
            ->where('salary_type', $salary_type)
            ->orderByDesc('revert_count')
            ->first();
    }

    private function applyRevertData($revertSalaryDataWithCat, $salary_type)
    {
        $field  = $this->getFieldBySalaryType($salary_type);
        foreach ($revertSalaryDataWithCat->salary_data as $sectorSalaryStepsId => $salaryData) {
            foreach ($salaryData as $category => $salary) {
                MinimumSalary::where('sector_salary_steps_id', $sectorSalaryStepsId)
                    ->where('category_number', $category)
                    ->update([$field => $salary]);
            }
        }
        $revertSalaryDataWithCat->delete();
    }

    private function getCategoryMismatchMessage($sectorSalaryConfig, $revertCategory)
    {
        return "The category in the current sector ({$sectorSalaryConfig->category}) is mismatching with the category ($revertCategory) in the revert data";
    }

}