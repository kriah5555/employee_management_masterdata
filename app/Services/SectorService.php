<?php

namespace App\Services;

use App\Models\Sector\Sector;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType\EmployeeType;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;
use App\Models\Sector\SectorAgeSalary;
use App\Services\BaseService;
use App\Services\EmployeeTypeService;
use App\Models\MinimumSalary;

class SectorService
{
    protected $employeeTypeService;

    public function __construct(EmployeeTypeService $employeeTypeService)
    {
        $this->employeeTypeService = $employeeTypeService;
    }

    public function getSectorById($id)
    {
        return Sector::findOrFail($id);
    }

    public function getSectorDetails($id)
    {
        $sector = Sector::with([
            'employeeTypes',
            'salaryConfig',
            'salaryConfig.salarySteps',
            'sectorAgeSalary',
        ])->findOrFail($id);
        $temp = [];
        foreach($sector->sectorAgeSalary as $data) {
            $temp[$data->age] = $data->percentage;
        }
        $sector->age = $temp;
        return $sector;
    }

    public function getAllSectors()
    {
        return Sector::all();
    }

    public function getActiveSectors()
    {
        return Sector::where('status', true)->get();
    }

    public function createNewSector($values)
    {
        try {
            DB::beginTransaction();
            $sector = Sector::create($values);
            if (array_key_exists('employee_types', $values)) {
                $employee_types = $values['employee_types'];
            } else {
                $employee_types = [];
            }
            $sector->employeeTypes()->sync($employee_types);
            $sector_salary_config = $this->createSectorSalaryConfig($sector, $values['category'], count($values['experience']));
            $this->updateSectorSalarySteps($sector_salary_config, $values['experience']);
            $this->updateSectorAgeSalary($sector, $values['age']);
            DB::commit();
            return $sector;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createSectorSalaryConfig(Sector $sector, $categories, $steps)
    {
        $sector_salary_config = SectorSalaryConfig::firstOrCreate(['sector_id' => $sector->id]);
        $sector_salary_config->category = $categories;
        $sector_salary_config->steps = $steps;
        $sector_salary_config->save();
        return $sector_salary_config;
    }

    public function createSectorAgeSalary(Sector $sector, $age_values)
    {
        foreach($age_values as $data) {
            $sector_salary_step = SectorSalarySteps::firstOrCreate([
                'sector_id' => $sector->id,
                'age' => $data['level']
            ]);
            $sector_salary_step->from = $data['from'];
            $sector_salary_step->to = $data['to'];
            $sector_salary_step->save();
        }
    }

    public function updateSector(Sector $sector, $values)
    {
        try {
            DB::beginTransaction();
            $sector->update($values);
            if (array_key_exists('employee_types', $values)) {
                $employee_types = $values['employee_types'];
            } else {
                $employee_types = [];
            }
            $sector->employeeTypes()->sync($employee_types);
            $sector_salary_config = $this->updateSectorSalaryConfig($sector, $values['category'], count($values['experience']));
            $this->updateSectorSalarySteps($sector_salary_config, $values['experience']);
            $this->updateSectorAgeSalary($sector, $values['age']);
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateSectorSalaryConfig(Sector $sector, $categories, $steps)
    {
        $sector_salary_config = SectorSalaryConfig::where('sector_id', $sector->id)->firstOrFail();
        $sector_salary_config->category = $categories;
        $sector_salary_config->steps = $steps;
        $sector_salary_config->save();
        return $sector_salary_config;
    }

    public function updateSectorSalarySteps(SectorSalaryConfig $sector_salary_config, $experience)
    {
        $categories = $sector_salary_config->category;
        SectorSalarySteps::where('sector_salary_config_id', $sector_salary_config->id)
        ->where('level', '>=', $sector_salary_config->steps)->delete();
        foreach($experience as $data) {
            $sector_salary_step = SectorSalarySteps::firstOrCreate([
                'sector_salary_config_id' => $sector_salary_config->id,
                'level' => $data['level']
            ]);
            $sector_salary_step->from = $data['from'];
            $sector_salary_step->to = $data['to'];
            $sector_salary_step->save();
            foreach (range(1, $categories) as $category_number) {
                $minimum_salary = MinimumSalary::firstOrCreate([
                    'sector_salary_step_id' => $sector_salary_step->id,
                    'category_number' => $category_number
                ]);
                if ($minimum_salary->wasRecentlyCreated) {
                    $minimum_salary->salary = 0;
                    $minimum_salary->save();
                }
            }
        }
    }

    public function updateSectorAgeSalary(Sector $sector, $age_values)
    {
        $age = array_keys($age_values);
        SectorAgeSalary::where('sector_id', $sector->id)
        ->whereNotIn('age', $age)->delete();
        foreach($age_values as $age => $percentage) {
            $sector_age_salary = SectorAgeSalary::firstOrCreate([
                'sector_id' => $sector->id,
                'age' => $age
            ],[
                'percentage' => $percentage
            ]);
            $sector_age_salary->percentage = $percentage;
            $sector_age_salary->save();
        }
    }

    public function getEmployeeTypesBySector(Sector $sector)
    {
        return $sector->employeeTypes;
    }

    public function getSectorSalaryConfig(Sector $sector)
    {
        return $sector->salaryConfig;
    }

    public function getSectorSalarySteps(SectorSalaryConfig $salary_config)
    {
        return $salary_config->salarySteps;
    }

    public function getCreateSectorOptions()
    {
        $options['employee_types'] = $this->employeeTypeService->getEmployeeTypeOptions();
        return $options;
    }

    public function getSectorOptions()
    {
        $options = [];
        $employee_types = Sector::where('status', '=', true)->get();
        foreach ($employee_types as $value) {
            $options[] = [
                'value' => $value['id'],
                'label' => $value['name'],
            ];
        }
        return $options;
    }

    public function getCategoriesForSector()
    {
        $data = [];
        $sectors = Sector::where('status', true)->with('salaryConfig')->get();
        foreach ($sectors as $item) {
            $data[$item->id] = $item->salaryConfig->category;
        }
        return $data;
    }

}
