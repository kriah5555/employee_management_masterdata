<?php

namespace App\Services\Sector;

use App\Models\Sector\Sector;
use App\Models\Sector\SectorDimonaCode;
use Illuminate\Support\Facades\DB;
use App\Models\Sector\SectorSalaryConfig;
use App\Models\Sector\SectorSalarySteps;
use App\Models\Sector\SectorAgeSalary;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Services\EmployeeFunction\FunctionService;
use App\Models\MinimumSalary;
use App\Repositories\Sector\SectorRepository;
use App\Repositories\Sector\SectorSalaryConfigRepository;

class SectorService
{
    protected $employeeTypeService;

    protected $sectorRepository;

    protected $sectorSalaryConfigRepository;

    public function __construct(SectorRepository $sectorRepository, SectorSalaryConfigRepository $sectorSalaryConfigRepository, EmployeeTypeService $employeeTypeService)
    {
        $this->sectorRepository = $sectorRepository;
        $this->sectorSalaryConfigRepository = $sectorSalaryConfigRepository;
        $this->employeeTypeService = $employeeTypeService;
    }

    public function getSectorDetails($id)
    {
        return $this->formatSectors($this->sectorRepository->getSectorById($id, [
            'employeeTypes',
            'salaryConfig',
            'salaryConfig.salarySteps',
            'sectorAgeSalary',
            'sectorDimonaCodes.employeeType'
        ]));
    }

    public function formatSectors($sector)
    {
        $sector = $sector->toArray();
        $response = [];
        foreach ($sector['sector_dimona_codes'] as $value) {
            $response[$value['employee_type']['id']] = $value['dimona_code'];
        }
        $sector['dimona_codes'] = $response;
        return $sector;
    }


    public function getSectors()
    {
        return $this->sectorRepository->getSectors();
    }
    public function getActiveSectors()
    {
        return $this->sectorRepository->getActiveSectors();
    }
    public function getActiveSectorsOptions()
    {
        $employeeTypes = $this->getActiveSectors();
        return $employeeTypes->map(function ($item) {
            return [
                'value' => $item->id,
                'label' => $item->name,
                // Add more fields as needed
            ];
        })->toArray();
    }

    public function createSector($values)
    {
        return DB::transaction(function () use ($values) {
            $sector = $this->sectorRepository->createSector($values);
            $this->sectorRepository->updateSectorEmployeeTypes($sector, $values['employee_types']);
            $sector_salary_config = $this->updateSectorSalaryConfig($sector, $values['category'], count($values['experience']));
            $this->updateSectorSalarySteps($sector_salary_config, $values['experience']);
            $this->updateSectorAgeSalary($sector, $values['age']);
            foreach ($values['dimona_codes'] as $employeeTypeId => $dimonaCode) {
                $dimonaCodeObj = SectorDimonaCode::firstOrCreate([
                    'employee_type_id' => $employeeTypeId,
                    'sector_id'        => $sector->id,
                ]);
                $dimonaCodeObj->dimona_code = $dimonaCode;
                $dimonaCodeObj->save();
            }
            return $sector;
        });
    }

    public function updateSectorSalaryConfig(Sector $sector, $categories, $steps)
    {
        $sectorSalaryConfig = $this->sectorSalaryConfigRepository->getOrCreateSectorSalaryConfig($sector->id);
        $this->sectorSalaryConfigRepository->updateSectorSalaryConfig($sectorSalaryConfig, ['category' => $categories, 'steps' => $steps]);
        return $sectorSalaryConfig;
    }

    public function updateSector(Sector $sector, $values)
    {
        return DB::transaction(function () use ($sector, $values) {
            $this->sectorRepository->updateSector($sector, $values);
            $this->sectorRepository->updateSectorEmployeeTypes($sector, $values['employee_types']);
            $sector_salary_config = $this->updateSectorSalaryConfig($sector, $values['category'], count($values['experience']));
            $this->updateSectorSalarySteps($sector_salary_config, $values['experience']);
            $this->updateSectorAgeSalary($sector, $values['age']);
            foreach ($values['dimona_codes'] as $employeeTypeId => $dimonaCode) {
                $dimonaCodeObj = SectorDimonaCode::firstOrCreate([
                    'employee_type_id' => $employeeTypeId,
                    'sector_id'        => $sector->id,
                ]);
                $dimonaCodeObj->dimona_code = $dimonaCode;
                $dimonaCodeObj->save();
            }
            return $sector;
        });
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
                    $minimum_salary->monthly_minimum_salary = 0;
                    $minimum_salary->hourly_minimum_salary = 0;
                    $minimum_salary->save();
                }
            }
        }
    }

    public function updateSectorAgeSalary(Sector $sector, $value)
    {
        foreach ($value as $val) {
            $age_values[$val['age']] = [
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

    public function getCategoriesForSector()
    {
        $data = [];
        $sectors = Sector::where('status', true)->with('salaryConfig')->get();
        foreach ($sectors as $item) {
            $data[$item->id] = $item->salaryConfig->category;
        }
        return $data;
    }

    public function deleteSector(Sector $sector)
    {
        return $this->sectorRepository->deleteSector($sector);
    }

    public function getSectorFunctionTitles(array $sector_ids)
    {
        $function_service = app(FunctionService::class);
        return ['function_titles' => $function_service->getFunctionTitlesLinkedToSectors($sector_ids)];
    }
}
