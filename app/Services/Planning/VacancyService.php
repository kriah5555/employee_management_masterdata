<?php

namespace App\Services\Planning;

use App\Interfaces\Planning\VacancyInterface;
use App\Models\Company\{
    Company,
    Location,
    Workstation,
    WorkstationToFunctions,
    Employee\EmployeeProfile
};
use App\Models\Planning\{Vacancy, VacancyEmployeeTypes, VacancyPostEmployees};
use App\Models\User\CompanyUser;

class VacancyService implements VacancyInterface
{
    const REPEAT_TYPE=[0 => 'No repeat', 1 => 'Daily', 2 =>  'Weekly', 3 => 'Monthly'];
    const REQUEST_STATUS= [0 => 'Applied', 1 => 'Approved', 2 => 'Rejected', 3 => 'Saved', 4 => 'Ignored'];

    public function __construct(
        protected Vacancy $vacancy,
        protected Workstation $workStation,
        protected Company $company,
        protected Location $location,
        protected PlanningService $planningService,
        protected VacancyPostEmployees $vacancyPostEmployees,
        protected CompanyUser $companyUser
    ) {}

    public function formatCreateVacancy(&$data)
    {
        $response = [];
        $response['name'] = $data['name'] ?? 'title';
        $response['location_id'] = $data['location'];
        $response['workstation_id'] = $data['workstations'];
        $response['function_id'] = $data['functions'];
        $response['start_date'] = date('Y-m-d', strtotime($data['start_date']));
        $response['start_time'] = $data['start_time'].':00';
        $response['end_time'] = $data['end_time'].':00';
        $response['vacancy_count'] = $data['count'];
        $response['approval_type'] = $data['approval_type'];
        $response['extra_info'] = $data['extra_info'];
        $response['status'] = $data['status'];
        $response['repeat_type'] = $data['repeat_type'];

        if (!empty($data['end_date'])) {
            $response['end_date'] =  date('Y-m-d', strtotime($data['end_date']));
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
            $response[$value['id']] = $temp['functions'];
        }
        return $response;
    }

    public function vacancyOptions($companyId)
    {
        $data = [];
        $data['locations'] = $this->location->all(['id as value', 'location_name as label'])->toArray();
        
        /*$data['workstations'] = array_map( function($workstations) {
            return reset($workstations) ?? [];
	}, $this->planningService->getWorkstations());*/
        $data['workstations'] = $this->planningService->getWorkstations();
        $data['workstationsFunctions'] = $this->getFormatedFunction();
        $data['employeeTypes'] = $this->planningService->getEmployeeTypes($companyId);

        return $data;
    }

    public function filterVacancies(&$vacancies, $filters)
    {
        // Filter by location
        // if (isset($filters['location']) && count($filters['location']) > 0) {
        //     $vacancies->whereIn('location_id', $filters['location']);
        // }

        // Filter by functions
        // if (isset($filters['functions']) && count($filters['functions']) > 0) {
        //     $vacancies->whereIn('function_id', $filters['functions']);
        // }

        // Filter by employee types
        // if (isset($filters['employee_types']) && count($filters['employee_types']) > 0) {
        //     $employeeTypesFilter = $filters['employee_types'];
        //     $vacancies->whereHas('employeeTypes', function ($query) use ($employeeTypesFilter) {
        //         $query->whereIn('employee_types_id', $employeeTypesFilter);
        //     });
        // }

        // Filter by status
        if (isset($filters['status']) && $filters['status'] != '') {
            $vacancies->where('status', $filters['status']);
        }

        // Filter by start date range
        // if (isset($filters['start_date']) && !empty($filters['start_date'])) {
        //     $vacancies->where('start_date', '>=', $filters['start_date'])
        //     ->orWhere('end_date', '>=', $filters['start_date']);
        //     $vacancies->orWhere('start_date', '>=', $filters['start_date']);
        // }

        // if (isset($filters['order_by']) && !empty($filters['order_by']) > 0) {
        //     $order = $filters['order_type'] ?? 'asc';
        //     $vacancies->orderBy($filters['order_by'], $order);
        // }
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

    public function getVacancyById($vacancies)
    {
        $response = $vacanciesRaw =[];
        if (!empty($vacancies)) {
            $query = $this->vacancy->with(
                    'location',
                    'workstations',
                    'functions',
                    'employeeTypes.employeeType',
                    'vacancyPostEmployees.employeeProfile.employeeBasicDetails'
                );
            $query->findOrFail($vacancies);
            $vacanciesRaw = $query->get()->toArray();
            $this->formatVacancies($vacanciesRaw, $response);
        }
        return $response[0] ?? $response;
    }

    public function formatVacancies($data, &$response)
    {
        if (count($data) > 0) {
            foreach ($data as $value) {
                $temp = [];
                $temp['vacancy_id'] = $value['id'];
                $temp['name'] = $value['name'];
                $temp['location_id'] = $value['location_id'];
                $temp['location_name'] = $value['location'] ? $value['location']['location_name'] ?? '': '';
                $temp['workstation_id'] = $value['workstations'] ? $value['workstations']['id'] ?? '' : '';
                $temp['workstation_name'] = $value['workstations'] ? $value['workstations']['workstation_name'] ?? '' : '';
                $temp['extra_info'] = $value['extra_info'];
                $temp['vacancy_count'] = $value['vacancy_count'];
                $temp['status'] = $value['status'];
                $temp['start_date'] = $value['start_date'];
                $temp['start_time'] = $value['start_time'];
                $temp['end_time'] = $value['end_time'];
                $temp['repeat_type'] = $value['repeat_type'];
                $temp['repeat_title'] = self::REPEAT_TYPE[$value['repeat_type']];
                $temp['function_id'] = $value['function_id'];
                $temp['function_name'] = $value['functions']['name'];
                $temp['total'] = $value['vacancy_count'];
                $temp['applied'] = count($value['vacancy_post_employees']);
                $temp['responded'] = array_filter($value['vacancy_post_employees'], function($employee) {
                    return $employee['request_status']!= 0;
                });
                $temp['responded'] = count($temp['responded']);
                $temp['employee_types'] = array_map(function($employeeType) {
                    return [
                        'label' => $employeeType['employee_type']['name'],
                        'value' => $employeeType['employee_types_id'],
                    ];
                }, $value['employee_types']);

                $temp['employees'] = array_map(function($data) {
                        $employee_basic_details = $data['employee_profile']['employee_basic_details'];
                        return [
                            'application_id' => $data['id'],
                            'vacancy_id' => $data['vacancy_id'],
                            'employee_id' => $data['employee_profile_id'],
                            'employee_name' => $employee_basic_details['first_name']. ' '.$employee_basic_details['last_name'],
                            'status_name' =>  self::REQUEST_STATUS[$data['request_status']],
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

    public function getEmployeeProfileFromUserAndCompanyId($userId, $companyId)
    {
        return EmployeeProfile::select('id')
        ->where('user_id', $userId)
        ->get()
        ->all();
    }

    public function applyVacancyService($data)
    {
        $userId = $companyId = '';
        if (empty($data['employee_profile_id'])) {
            $userId = $data['user_id'];
            $companyId = $data['company_id'] ?? 0;
            if (empty($companyId) || !connectCompanyDataBase($data['company_id'])) {
              throw new \Exception('Issue with company Id');
            }
            $employeeProfileId = $this->getEmployeeProfileFromUserAndCompanyId($data['user_id'], $data['company_id']);
            unset($data['user_id'], $data['company_id']);
            $data['employee_profile_id'] = $employeeProfileId[0]->id ?? 0;
            $data['request_at'] = now()->format('Y-m-d H:i:s');
            $data['vacancy_date'] = date('Y-m-d', strtotime($data['vacancy_date']));
        }
        return $this->vacancyPostEmployees->updateOrCreate(
            [
                'vacancy_date' => $data['vacancy_date'],
                'vacancy_id' => $data['vacancy_id'],
                'employee_profile_id' => $data['employee_profile_id'],
            ],
            $data
        );
    }

    public function replyToVacancyService($data)
    {
        $query = $this->vacancyPostEmployees->findOrFail($data['id']);
        // $data['responded_by'] = $data['responded_by'];
        $data['responded_at'] = now()->format('Y-m-d H:i:s');
        return $query->update($data);
    }

    public function getAvailableJobsOfEmployee()
    {

    }

    public function getEmployeeOverviewService($userId)
    {
        $userCompany = $this->companyUser->getCompanyDetails($userId)->get()->toArray();
        foreach($userCompany as $value) {
            if (empty($value['company_id']) || !connectCompanyDataBase($value['company_id'])) {
                throw new \Exception('Issue with company Id');
            }
        }

    }
}
