<?php

namespace App\Services;

use App\Models\Sector;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType;
use App\Models\SectorSalaryConfig;
use App\Models\SectorSalarySteps;
use App\Models\SectorAgeSalary;
use App\Services\BaseService;

class SectorService
{
    public function getSectorDetails($id)
    {
        $sector = Sector::with([
            'employeeTypes',
            'salaryConfig',
            'salaryConfig.salarySteps',
            'sectorAgeSalary',
            ])->findOrFail($id);
        // $sector = Sector::with('employeeTypes')->with('salaryConfig')->with('salaryConfig.salarySteps')->with('sectorAgeSalary')->findOrFail($id);
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
            $sector = $this->store($values);
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

    public function store($values)
    {
        $sector = Sector::create($values);
        if (array_key_exists('employee_types', $values)) {
            $employee_types = $values['employee_types'];
        } else {
            $employee_types = [];
        }
        $sector->employeeTypes()->sync($employee_types);
        return $sector;
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
            $this->update($sector, $values);
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

    public function update(Sector $sector, $values)
    {
        $sector->update($values);
        if (array_key_exists('employee_types', $values)) {
            $employee_types = $values['employee_types'];
        } else {
            $employee_types = [];
        }
        $sector->employeeTypes()->sync($employee_types);
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
        foreach($experience as $data) {
            $sector_salary_step = SectorSalarySteps::firstOrCreate([
                'sector_salary_config_id' => $sector_salary_config->id,
                'level' => $data['level']
            ]);
            $sector_salary_step->from = $data['from'];
            $sector_salary_step->to = $data['to'];
            $sector_salary_step->save();
        }
    }

    public function updateSectorAgeSalary(Sector $sector, $age_values)
    {
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
}
