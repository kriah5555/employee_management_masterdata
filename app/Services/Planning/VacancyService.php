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
use App\Services\CompanyService;

class VacancyService implements VacancyInterface
{
    const REPEAT_TYPE = [0 => 'No repeat', 1 => 'Daily', 2 => 'Weekly', 3 => 'Monthly'];
    const REQUEST_STATUS = [0 => 'Applied', 1 => 'Approved', 2 => 'Rejected', 3 => 'Saved', 4 => 'Ignored'];

    public function __construct(
        protected Vacancy $vacancy,
        protected Workstation $workStation,
        protected Company $company,
        protected Location $location,
        protected PlanningService $planningService,
        protected VacancyPostEmployees $vacancyPostEmployees,
        protected CompanyUser $companyUser,
        protected EmployeeProfile $employeeProfile
    ) {
    }

    public function formatCreateVacancy(&$data)
    {
        $response = [];
        $response['name'] = $data['name'] ?? 'title';
        $response['location_id'] = $data['location'];
        $response['workstation_id'] = $data['workstations'];
        $response['function_id'] = $data['functions'];
        $response['start_date'] = date('Y-m-d', strtotime($data['start_date']));
        $response['start_time'] = $data['start_time'] . ':00';
        $response['end_time'] = $data['end_time'] . ':00';
        $response['vacancy_count'] = $data['vacancy_count'];
        $response['approval_type'] = $data['approval_type'];
        $response['extra_info'] = $data['extra_info'];
        $response['status'] = $data['status'];
        $response['repeat_type'] = $data['repeat_type'];

        if (!empty($data['end_date'])) {
            $response['end_date'] = date('Y-m-d', strtotime($data['end_date']));
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

        return $vacancy;
    }

    public function updateVacancyService($data, $vacancy)
    {
        $this->formatCreateVacancy($data);
        foreach($data['formated']['employee_types'] as $employeeType) {
            $updateEmployeeType[] = [
                'employee_types_id' => $employeeType['employee_types_id'],
                'vacancy_id' => $vacancy
            ];
        }
        $vacancy = Vacancy::findOrFail($vacancy);
        $vacancy->update($data['formated']['vacancy']);
        $vacancy->employeeTypes()->delete();
        $vacancy->employeeTypes()->createMany($updateEmployeeType);
        return $vacancy->employeeTypes;
    }

    public function getFormatedFunction()
    {
        $data = $response = [];
        $data = $this->workStation->with(['functionTitles'])->get()->toArray();
        foreach ($data as $value) {
            $temp = [];
            $temp['id'] = $value['id'];
            $temp['name'] = $value['workstation_name'];
            $temp['functions'] = array_map(function ($functionTitles) {
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

        // Filter by start date range.
        /*if (isset($filters['start_date']) && !empty($filters['start_date'])) {
            $startDate = $filters['start_date'];
            $vacancies->where(function ($query) use ($startDate) {
                $query->where('start_date', '>=', $startDate)
                ->orWhere('end_date', '>=', $startDate)
                ->orWhereNull('end_date');
            });
            // $vacancies->orWhere('start_date', '>=', $filters['start_date']);
    }*/

        /* if (isset($filters['employees']) && !empty($filters['employees'])) {
             $employees = $filters['employees'];
             $vacancies->whereHas('vacancyPostEmployees', function ($query) use ($employees) {
                 $query->orWhere('employee_profile_id', '=', $employees);
             });
     }*/

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
        $response = $vacanciesRaw = [];
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
                $temp['location_name'] = $value['location'] ? $value['location']['location_name'] ?? '' : '';
                $temp['workstation_id'] = $value['workstations'] ? $value['workstations']['id'] ?? '' : '';
                $temp['workstation_name'] = $value['workstations'] ? $value['workstations']['workstation_name'] ?? '' : '';
                $temp['extra_info'] = $value['extra_info'];
                $temp['vacancy_count'] = $value['vacancy_count'];
                $temp['status'] = $value['status'];
                $temp['start_date'] = date('d-m-Y', strtotime($value['start_date']));
                $temp['start_time'] = date('H:i', strtotime($value['start_time']));
                $temp['end_time'] = date('H:i', strtotime($value['end_time']));
                $temp['repeat_type'] = $value['repeat_type'];
                $temp['repeat_title'] = self::REPEAT_TYPE[$value['repeat_type']];
                $temp['function_id'] = $value['function_id'];
                $temp['function_name'] = $value['functions']['name'];
                $temp['total'] = $value['vacancy_count'];
                $temp['not_responded'] = array_filter($value['vacancy_post_employees'], function ($employee) {
                    return $employee['request_status'] == 0;
                });
		$temp['not_responded'] = count($temp['not_responded']);
                $temp['responded'] = array_filter($value['vacancy_post_employees'], function ($employee) {
                    return ($employee['request_status'] != 0 && $employee['request_status'] != 3 && $employee['request_status'] != 4);
                });
                $temp['responded'] = count($temp['responded']);
                $temp['accepted'] = count(array_filter($value['vacancy_post_employees'], function($data) {
                    return $data['request_status'] == 1;
                }));
                $temp['rejected'] = count(array_filter($value['vacancy_post_employees'], function($data) {
                    return $data['request_status'] == 2;
                }));
		$temp['employee_types'] = array_map(function ($employeeType) {
                    return [
                        'label' => $employeeType['employee_type']['name'],
                        'value' => $employeeType['employee_types_id'],
                    ];
                }, $value['employee_types']);

                $temp['employees'] = array_map(
                    function ($data) {
			$employee_basic_details = $data['employee_profile']['employee_basic_details'];
                        if ($data['request_status'] != 3 && $data['request_status'] != 4) {
                            return [
                                'application_id' => $data['id'],
                                'vacancy_id'     => $data['vacancy_id'],
                                'employee_id'    => $data['employee_profile_id'],
                                'employee_name'  => $employee_basic_details['first_name'] . ' ' . $employee_basic_details['last_name'],
                                'status_name'    => self::REQUEST_STATUS[$data['request_status']],
                                'status'         => $data['request_status'],
                                'request_at'     => $data['request_at'],
                                'responded_by'   => $data['responded_by'],
                                'vacancy_date'   => date('d-m-Y', strtotime($data['vacancy_date']))
			    ];
			}
                    },
                    $value['vacancy_post_employees']
		);
		$temp['employees'] = array_values(array_filter($temp['employees'], function($data) { return $data != null;}));		
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
            $data['status'] = 0;
        }

        return $this->vacancyPostEmployees->updateOrCreate(
            [
                'vacancy_date'        => $data['vacancy_date'],
                'vacancy_id'          => $data['vacancy_id'],
                'employee_profile_id' => $data['employee_profile_id'],
            ],
            $data
        );
    }

    /**
     * Update the job status
     *
     * @param  [type] $data
     * @return void
     */
    public function replyToVacancyService($data)
    {
        $query = $this->vacancyPostEmployees->findOrFail($data['id']);
        // $data['responded_by'] = $data['responded_by'];
        $data['responded_at'] = now()->format('Y-m-d H:i:s');
        $data['vacancy_date'] = date('Y-m-d', strtotime($data['vacancy_date']));
        return $query->update($data);
    }

    /**
     * The function formate jobs data for the employee overview.
     *
     * @param  [type] $vacancies
     * @param  [type] $employeeProfileId
     * @param  [type] $response
     *
     */
    public function formatEmployeeJobsOverview($companyDetails, $vacancies, $employeeProfileId, &$response)
    {
        foreach ($vacancies as $vacancy) {
            $isNew = true;
            $status = 0;
            $job = [];
            if (count($vacancy['employees']) > 0) {
                $job = array_filter(
                    $vacancy['employees'],
                    function ($employeeJob) use ($employeeProfileId) {
                        return ($employeeJob['employee_id'] == $employeeProfileId);
                    }
                );
                if (count($job) > 0) {
                    $job = reset($job);
                    $vacancy['employees'] = $job;
                    $status = isset($job['status']) ? $job['status'] : '';
                    $isNew = false;
                } else {
                    unset($vacancy['employees']);
                    $isNew = true;
                }
            }
            $company = app(CompanyService::class)->getCompanyById($companyDetails['company_id']);
            $vacancy['company_id'] = $company->id;
            $vacancy['company_name'] = $company->company_name;
            $vacancy['company_logo'] = null;

            $vacancy['saved'] = $status == 3 ? 1 : 0;
            $vacancy['start_date'] = date('d-m-Y', strtotime($vacancy['start_date']));

            $vacancy['plan_available'] = 0;
            if ($isNew == true) {
                $response['new'][] = $vacancy;
            } else {
                // Applied, Approved, Rejected.
                if ($status == 0 || $status == 1 || $status == 2) {
                    $response['applied'][] = $vacancy;
                }
                // Saved
                if ($status == 3) {
                    $response['saved'][] = $vacancy;
                }
                //Ignored.
                if ($status == 4) {
                    $response['ignored'][] = $vacancy;
                }
            }
        }
    }

    /**
     * Employee overview Service.
     *
     * @param  [type] $userId
     * @return array
     */
    public function getEmployeeOverviewService($userId)
    {
        $response = ['new' => [], 'applied' => [], 'saved' => [], 'ignored' => []];

        $userCompany = $this->companyUser->getCompanyDetails($userId)->get()->toArray();

        foreach ($userCompany as $value) {
            if (empty($value['company_id']) || !connectCompanyDataBase($value['company_id'])) {
                throw new \Exception('Issue with company Id');
            } else {
                $employeeProfile = $this->employeeProfile->getEmployeeProfileByUserId($value['user_id'])->toArray();
                if (count($employeeProfile) > 0) {
                    $employeeProfile = reset($employeeProfile)['id'];
                    $date = now()->format('Y-m-d');

                    $filters = [
                        'start_date' => $date,
                        'status'     => 1,
                        'employees'  => $employeeProfile
                    ];

                    $availableJobs = $this->getVacancies($filters);

                    $this->formatEmployeeJobsOverview($value, $availableJobs, $employeeProfile, $response);
                }
            }
        }
        return $response;
    }
}
