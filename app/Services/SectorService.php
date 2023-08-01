<?php

namespace App\Services;

use App\Models\Sector;
use Illuminate\Support\Facades\DB;
use App\Models\EmployeeType;
use App\Models\SectorSalaryConfig;
use App\Models\SectorSalarySteps;

class SectorService
{
    public function getSectorDetails($id)
    {
        return Sector::findOrFail($id);
    }

    public function getAllSectors()
    {
        return Sector::all();
    }

    public function getActiveSectors()
    {
        return Sector::where('status', '=', true)->get();
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
            $sector_salary_config = SectorSalaryConfig::firstOrCreate(['sector_id' => $sector->id]);
            $sector_salary_config->category = $values['category'];
            $sector_salary_config->steps = count($values['experience']);
            $sector_salary_config->save();
            foreach($values['experience'] as $data) {
                $sector_salary_step = SectorSalarySteps::firstOrCreate([
                    'sector_salary_config_id' => $sector_salary_config->id,
                    'step_number' => $data['level']
                ]);
                $sector_salary_step->from = $data['from'];
                $sector_salary_step->to = $data['to'];
                $sector_salary_step->save();
            }
            DB::commit();
            return $sector;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
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
            $sector_salary_config = SectorSalaryConfig::firstOrCreate(['sector_id' => $sector->id]);
            $sector_salary_config->category = $values['category'];
            $sector_salary_config->steps = count($values['experience']);
            $sector_salary_config->save();
            foreach($values['experience'] as $data) {
                $sector_salary_step = SectorSalarySteps::firstOrCreate([
                    'sector_salary_config_id' => $sector_salary_config->id,
                    'step_number' => $data['level']
                ]);
                $sector_salary_step->from = $data['from'];
                $sector_salary_step->to = $data['to'];
                $sector_salary_step->save();
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getSectorForEdit(Sector $sector)
    {
        try {
            $sector->employeeTypes;
            $sector->salaryConfig;
            $sector->salaryConfig->salarySteps;
            return $sector;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
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
