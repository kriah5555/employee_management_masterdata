<?php

namespace App\Services\Sector;

use App\Models\Sector\Sector;
use Illuminate\Support\Facades\DB;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;
use App\Models\Sector\SectorAgeSalary;
use App\Services\EmployeeType\EmployeeTypeService;
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
        foreach ($sector->sectorAgeSalary as $data) {
            $temp[] = ['age' => $data->age, 'value' => $data->percentage];
        }
        $sector->age = $temp;
        return $sector;
    }

    public function show($id)
    {
        return $this->getSectorDetails($id);
    }


    public function edit($id)
    {
        $options = $this->create();
        $sector = Sector::findOrFail($id);
        $sector->employeeTypesValue;
        $sector->salaryConfig;
        $sector->salaryConfig->salarySteps;
        $sector->sectorAgeSalary;
        $options['details'] = $sector;
        return $options;
    }

    public function index()
    {
        return Sector::all();
    }

    public function getActiveSectors()
    {
        return Sector::where('status', true)->get();
    }

    public function store($values)
    {
        try {
            DB::beginTransaction();
                $sector = Sector::create($values);
                $sector->employeeTypes()->sync($values['employee_types'] ?? []);
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
        foreach ($age_values as $data) {
            $sector_salary_step = SectorSalarySteps::firstOrCreate([
                'sector_id' => $sector->id,
                'age'       => $data['level']
            ]);
            $sector_salary_step->from = $data['from'];
            $sector_salary_step->to = $data['to'];
            $sector_salary_step->save();
        }
    }

    public function update(Sector $sector, $values)
    {
        try {
            DB::beginTransaction();
            $sector->update($values);
            $sector->employeeTypes()->sync($values['employee_types'] ?? []);
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
        foreach ($experience as $data) {
            $sector_salary_step = SectorSalarySteps::firstOrCreate([
                'sector_salary_config_id' => $sector_salary_config->id,
                'level'                   => $data['level']
            ]);
            $sector_salary_step->from = $data['from'];
            $sector_salary_step->to = $data['to'];
            $sector_salary_step->save();
            foreach (range(1, $categories) as $category_number) {
                $minimum_salary = MinimumSalary::firstOrCreate([
                    'sector_salary_steps_id' => $sector_salary_step->id,
                    'category_number'        => $category_number
                ]);
                if ($minimum_salary->wasRecentlyCreated) {
                    $minimum_salary->salary = 0;
                    $minimum_salary->save();
                }
            }
        }
    }

    public function updateSectorAgeSalary(Sector $sector, $value)
    {
        foreach ($value as $val) {
            $age_values[$val['age']] =[
                "percentage"       => $val['percentage'],
                "max_time_to_work" => $val['max_time_to_work']
            ];
        }
        $age = array_keys($age_values);
        SectorAgeSalary::where('sector_id', $sector->id)
            ->whereNotIn('age', $age)->delete();
        foreach ($age_values as $age => $data) {
            $sector_age_salary = SectorAgeSalary::firstOrCreate([
                'sector_id' => $sector->id,
                'age'       => $age
            ], $data);
            $sector_age_salary->percentage = $data['percentage'];
            $sector_age_salary->max_time_to_work = $data['max_time_to_work'];
            $sector_age_salary->save();
        }
    }

    public function getSectorOptions()
    {
        return Sector::where('status', '=', true)
            ->select('id as value', 'name as label')
            ->get();
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

    public function create()
    {
        $options = [];
        $options['employee_types'] = $this->employeeTypeService->getEmployeeTypeOptions();
        return $options;
    }

}