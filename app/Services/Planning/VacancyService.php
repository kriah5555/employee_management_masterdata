<?php

namespace App\Services\Planning;

use App\Interfaces\Planning\VacancyInterface;
use App\Models\Company\Company;
use App\Models\Company\Location;
use App\Models\Company\Workstation;
use App\Models\Company\WorkstationToFunctions;
use App\Models\Planning\{Vacancy, VacancyEmployeeTypes};




class VacancyService implements VacancyInterface
{
    public function __construct(
        protected Vacancy $vacancy,
        protected Workstation $workStation,
        protected Company $company,
        protected Location $location,
        protected PlanningService $planningService
    ) {}

    public function formatCreateVacancy(&$data)
    {
        $response = [];
        $response['location_id'] = $data['location'];
        $response['workstation_id'] = $data['workstations'];
        $response['function_id'] = $data['functions'];
        $response['start_date'] = $data['start_date'];
        $response['start_time'] = $data['start_time'].':00';
        $response['end_time'] = $data['end_time'].':00';
        $response['vacancy_count'] = $data['count'];
        $response['approval_type'] = $data['approval_type'];
        $response['extra_info'] = $data['extra_info'];
        $response['status'] = $data['status'];
        $response['repeat_type'] = $data['repeat_type'];

        if (!empty($data['end_date'])) {
            $response['end_date'] =  $data['end_date'];
        }
        foreach ($data['employee_types'] as $et) {
            $data['formated']['employee_types'][] = ['employee_types_id' => $et];
        }
        $data['formated']['vacancy'] = $response;
    }

    public function createVacancies($data)
    {
        //Formatting the data.
        $this->formatCreateVacancy($data);
        //Creating the vacancy.
        $vacancy = Vacancy::create($data['formated']['vacancy']);
        //Linking with employee types.
        $vacancy->employeeTypes()->createMany($data['formated']['employee_types']);
        // foreach ($data['formated']['employee_types'] as $employeeType) {
        //     VacancyEmployeeTypes::create([
        //         'vacancy_id'  => $vacancy->id,
        //         'employee_types_id' => $employeeType,
        //     ]);
        // }
        
        return $vacancy;
    }

    public function getFormatedFunction()
    {
        $data = $response = [];
        $data = $this->workStation->with(['functionTitles'])->get()->toArray();
        foreach($data as $value) {
            $temp = [];
            $temp['id'] = $value['id'];
            $temp['name'] = $value['workstation_name'];
            $temp['functions'] = array_map(function($functionTitles) {
                if (count($functionTitles['function_title']) > 0) {
                    return [
                        'value' => $functionTitles['function_title']['id'],
                        'label' => $functionTitles['function_title']['name'],
                    ];
                }
            }, $value['function_titles']);
            $response[$value['id']] = $temp;
        }
        return $response;
    }

    public function vacancyOptions($companyId)
    {
        $data = [];
        $data['locations'] = $this->location->all(['id as value', 'location_name as label'])->toArray();
        $data['workstations'] = $this->planningService->getWorkstations();
        $data['workstationsFunctions'] = $this->getFormatedFunction();
        $data['employeeTypes'] = $this->planningService->getEmployeeTypes($companyId);

        return $data;
    }



    public function getVacancies()
    {
        Vacancy::with('location', 'workstations', 'employeeTypes.employeeType')->get()
    }
}
