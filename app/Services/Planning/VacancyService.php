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

    public function filterVacancies(&$vacancies, $filters)
    {
        // Filter by location
        if (isset($filters['location']) && count($filters['location']) > 0) {
            $vacancies->whereIn('location_id', $filters['location']);
        }

        // Filter by functions
        if (isset($filters['functions']) && count($filters['functions']) > 0) {
            $vacancies->whereIn('function_id', $filters['functions']);
        }

        // Filter by employee types
        if (isset($filters['employee_types']) && count($filters['employee_types']) > 0) {
            $employeeTypesFilter = $filters['employee_types'];
            $vacancies->whereHas('employeeTypes', function ($query) use ($employeeTypesFilter) {
                $query->whereIn('employee_types_id', $employeeTypesFilter);
            });
        }

        // Filter by status
        if (isset($filters['status']) && !empty($filters['status'])) {
            $vacancies->where('status', $filters['status']);
        }

        // Filter by start date range
        // if (isset($filters['start_date']) && !empty($filters['start_date'])) {
        //     $vacancies->where('start_date', '>=', $filters['start_date'])
        //     ->orWhere('end_date', '>=', $filters['start_date']);
        //     $vacancies->orWhere('start_date', '>=', $filters['start_date']);
        // }

        if (isset($filters['order_by']) && !empty($filters['order_by']) > 0) {
            $order = $filters['order_type'] ?? 'asc';
            $vacancies->orderBy($filters['order_by'], $order);
        }
    }

    public function getVacancies($filters)
    {
        $response = $vacanciesRaw = [];
        $vacancies = $this->vacancy->getVacancy();

        $this->filterVacancies($vacancies, $filters);
        $vacanciesRaw = $vacancies->get()->toArray();
        //Formating the data
        $this->formatVacancies($vacanciesRaw, $response);

        return $response;
    }

    public function formatVacancies($data, &$response)
    {
        if (count($data) > 0) {
            foreach ($data as $value) {
                $temp = [];
                $temp['location_id'] = $value['location_id'];
                $temp['location_name'] = $value['location'] ? $value['location']['location_name'] ?? '': '';
                $temp['workstation_id'] = $value['workstations'] ? $value['workstations']['location_name'] ?? '' : '';
                $temp['workstation_name'] = $value['workstations'] ? $value['workstations']['workstation_name'] ?? '' : '';
                $temp['employee_types'] = array_map(function($employeeType) {
                    return [
                        'label' => $employeeType['employee_type']['name'],
                        'value' => $employeeType['employee_types_id'],
                    ];
                }, $value['employee_types']);
                $temp['extra_info'] = $value['extra_info'];
                $temp['vacancy_count'] = $value['vacancy_count'];
                $temp['status'] = $value['status'];
                $temp['start_date'] = $value['start_date'];
                $temp['start_time'] = $value['start_time'];
                $temp['end_time'] = $value['end_time'];
                $temp['repeat_type'] = $value['repeat_type'];
                $temp['function_id'] = $value['function_id'];
                $temp['function_name'] = $value['functions']['name'];
                $temp['employees'] = array_map(function($data) {
                        $employee_basic_details = $data['employee_profile']['employee_basic_details'];
                        return [
                            'value' => $data['employee_profile_id'],
                            'label' => $employee_basic_details['first_name']. ' '.$employee_basic_details['last_name'],
                            'status' => $data['request_status'],
                            'request_at' => $data['request_at'],
                            'responded_by' => $data['responded_by'],
                        ];
                     }, $value['vacancy_post_employees']
                );
                $response[] = $temp;
            }
        }
    }
}
