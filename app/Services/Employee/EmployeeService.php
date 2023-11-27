<?php

namespace App\Services\Employee;

use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Employee\EmployeeProfile;
use App\Models\Company\Employee\LongTermEmployeeContract;
use App\Models\EmployeeType\EmployeeType;
use App\Models\User\CompanyUser;
use App\Models\User\UserBasicDetails;
use App\Models\User\UserContactDetails;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Repositories\Employee\EmployeeFunctionDetailsRepository;
use App\Repositories\Employee\EmployeeSocialSecretaryDetailsRepository;
use App\Services\CompanyService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Models\User\User;
use App\Repositories\Employee\EmployeeBenefitsRepository;
use App\Repositories\Company\LocationRepository;

use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\Email\MailService;

class EmployeeService
{
    // Mail::to('sunilgangadhar.infanion@gmail.com')->send(new SendMail('Test mails', $htmlContent));

    public function __construct(
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected EmployeeBenefitsRepository $employeeBenefitsRepository,
        protected EmployeeSocialSecretaryDetailsRepository $employeeSocialDetailsRepository,
        protected EmployeeFunctionDetailsRepository $employeeFunctionDetailsRepository,
        protected LocationRepository $locationRepository,
        protected UserService $userService,
        protected CompanyService $companyService,
        protected MailService $mailService
    ) {
    }
    /**
     * Function to get all the employee types
     */

    public function getAllEmployees()
    {
        try {
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function index()
    {
        $response = [];
        $employees = $this->employeeProfileRepository->getAllEmployeeProfiles([
            'user',
            'user.userBasicDetails',
            'user.userContactDetails',
        ]);
        foreach ($employees as $employee) {
            $employee->user;
            $employee->user->userBasicDetails;
            $employee->user->UserContactDetails;
            $currentDate = Carbon::now();

            $employeeContracts = $employee->employeeContracts->filter(function ($employeeContract) use ($currentDate) {
                return Carbon::parse($employeeContract->start_date)->lessThanOrEqualTo($currentDate) &&
                    (is_null($employeeContract->start_date) || Carbon::parse($employeeContract->end_date)->greaterThanOrEqualTo($currentDate));
            });
            $employeeDetails = $this->formatEmployeeListData($employee);
            if ($employeeContracts->isNotEmpty()) {
                $currentContract = $employeeContracts->first();
                if (!array_key_exists($currentContract->employeeType->id, $response)) {
                    $response[$currentContract->employeeType->id] = [
                        'employee_type' => $currentContract->employeeType->name,
                        'employees'     => []
                    ];
                }
                $response[$currentContract->employeeType->id]['employees'][] = $employeeDetails;
            } else {
                if (!array_key_exists(999, $response)) {
                    $response[999] = [
                        'employee_type' => 'No contracts',
                        'employees'     => []
                    ];
                }
                $response[999]['employees'][] = $employeeDetails;
            }
        }
        return array_values($response);
    }

    public function formatEmployeeListData($employee)
    {
        return $employee;
        $employeeDetails = [];
        $employeeDetails['employee_profile_id'] = $employee->id;
        $employeeDetails['first_name'] = $employee->user->userBasicDetails->first_name;
        $employeeDetails['last_name'] = $employee->user->userBasicDetails->last_name;
        $employeeDetails['phone_number'] = $employee->user->userContactDetails->phone_number;
        $employeeDetails['email'] = $employee->user->userContactDetails->email;
        $employeeDetails['social_security_number'] = $employee->user->social_security_number;
        // $employeeDetails['test'] = $employee;
        return $employeeDetails;
    }

    public function getEmployeeDetails(string $employeeProfileId)
    {
        return $this->formatEmployeeData($this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId, [
            'user',
            'user.userBasicDetails',
            'user.userContactDetails',
            'user.userFamilyDetails',
            'user.userBankAccount',
            'employeeSocialSecretaryDetails'
        ]));
    }

    public function formatEmployeeData($employee)
    {
        $userBasicDetails = $employee->user->userBasicDetails->toApiReponseFormat();
        $userBasicDetails['social_security_number'] = $employee->user->social_security_number;
        $userBasicDetails['date_of_birth'] = date('d-m-Y', strtotime($userBasicDetails['date_of_birth']));
        $userBasicDetails['license_expiry_date'] = $userBasicDetails['license_expiry_date'] != null ? date('d-m-Y', strtotime($userBasicDetails['license_expiry_date'])) : null;
        $userBasicDetails['gender'] = $employee->user->userBasicDetails->gender->toApiReponseFormat();
        $userBasicDetails['language'] = [
            'id'   => $userBasicDetails['language'],
            'name' => config('constants.LANGUAGE_OPTIONS')[$userBasicDetails['language']]
        ];
        $userAddressDetails = $employee->user->userAddress->toApiReponseFormat();
        $userContactDetails = $employee->user->userContactDetails->toApiReponseFormat();
        $userBankAccountDetails = $employee->user->userBankAccount->toApiReponseFormat();
        $userFamilyDetails = [
            'dependent_spouse' => [
                'id'   => $employee->user->userFamilyDetails->dependent_spouse,
                'name' => config('constants.DEPENDENT_SPOUSE_OPTIONS')[$employee->user->userFamilyDetails->dependent_spouse]
            ],
            'marital_status'   => $employee->user->userFamilyDetails->maritalStatus->toApiReponseFormat(),
            'children'         => 0
        ];
        return array_merge($userBasicDetails, $userAddressDetails, $userContactDetails, $userBankAccountDetails, $userFamilyDetails);
    }


    public function createNewEmployee($values, $company_id)
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();
            $existingEmpProfile = $this->userService->getUserBySocialSecurityNumber($values['social_security_number']);
            if ($existingEmpProfile->isEmpty()) {
                $user = $this->userService->createNewUser($values);
            } else {
                $user = $existingEmpProfile->last();
            }
            $user->assignRole('employee');
            $this->createCompanyUser($user, $company_id, 'employee');
            $employeeProfile = $this->createEmployeeProfile($user, $values);
            $this->createEmployeeSocialSecretaryDetails($employeeProfile, $values);
            $this->createEmployeeContract($employeeProfile, $values);
            $this->mailService->sendEmployeeCreationMail($employeeProfile->id);
            DB::connection('master')->commit();
            DB::connection('userdb')->commit();
            return $employeeProfile;
        } catch (Exception $e) {
            DB::connection('master')->rollback();
            DB::connection('userdb')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createNewResponsiblePerson($values, $company_id)
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();
            $existingEmpProfile = $this->userService->getUserBySocialSecurityNumber($values['social_security_number']);
            if ($existingEmpProfile->isEmpty()) {
                $user = $this->userService->createNewUser($values);
            } else {
                $user = $existingEmpProfile->last();
            }
            $user->assignRole($values['role']);
            $this->createCompanyUser($user, $company_id, $values['role']);
            $employeeProfile = $this->createEmployeeProfile($user, $values);
            $this->createEmployeeSocialSecretaryDetails($employeeProfile, $values);
            DB::connection('master')->commit();
            DB::connection('userdb')->commit();
            return $employeeProfile;
        } catch (Exception $e) {
            DB::connection('master')->rollback();
            DB::connection('userdb')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createCompanyUser(User $user, $company_id, $role)
    {
        $companyUser = CompanyUser::create([
            'user_id'    => $user->id,
            'company_id' => $company_id
        ]);
        $companyUser->assignRole($role);
    }

    public function createEmployeeProfile(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->employeeProfileRepository->createEmployeeProfile($values);
    }
    public function createEmployeeSocialSecretaryDetails(EmployeeProfile $employeeProfile, $values)
    {
        $values['employee_profile_id'] = $employeeProfile->id;
        return $this->employeeSocialDetailsRepository->createEmployeeSocialSecretaryDetails($values);
    }

    public function createEmployeeContract($employeeProfile, $values)
    {
        $contractDetails = $values['employee_contract_details'];
        $functionDetails = $values['employee_function_details'];
        $contractDetails['employee_profile_id'] = $employeeProfile->id;
        $contractDetails['weekly_contract_hours'] = str_replace(',', '.', $contractDetails['weekly_contract_hours']);
        $employeeType = EmployeeType::findOrFail($contractDetails['employee_type_id']);
        $contractDetails['start_date'] = date('Y-m-d', strtotime($contractDetails['start_date']));
        if (array_key_exists('end_date', $contractDetails) && $contractDetails['end_date'] != '') {
            $contractDetails['end_date'] = date('Y-m-d', strtotime($contractDetails['end_date']));
        }
        $employeeContract = EmployeeContract::create($contractDetails);
        if ($employeeType->employeeTypeCategory->id == 1) {
            $contractDetails['employee_contract_id'] = $employeeContract->id;
            LongTermEmployeeContract::create($contractDetails);
        }

        foreach ($functionDetails as $function) {
            $this->createEmployeeFunctionDetails($employeeContract, $function);
        }
    }
    public function createEmployeeFunctionDetails(EmployeeContract $employeeContract, $values)
    {
        $values['employee_contract_id'] = $employeeContract->id;
        return $this->employeeFunctionDetailsRepository->createEmployeeFunctionDetails($values);
    }

    public function createUser($values)
    {
        $username = $values['first_name'] . $values['last_name'];
        $username = strtolower(str_replace(' ', '', $username));
        $password = ucfirst($username) . '$';
        $saveValues = [
            'username'               => generateUniqueUsername($username),
            'password'               => Hash::make($password),
            'social_security_number' => $values['social_security_number'],
        ];
        return User::create($saveValues);
    }

    public function getSubTypeOptions()
    {
        return config('constants.SUB_TYPE_OPTIONS');
    }

    public function getScheduleTypeOptions()
    {
        return config('constants.SCHEDULE_TYPE_OPTIONS');
    }

    public function getEmploymentTypeOptions()
    {
        return config('constants.EMPLOYMENT_TYPE_OPTIONS');
    }

    public function getEmployeeSalaryTypeOptions()
    {
        return config('constants.EMPLOYEE_SALARY_TYPE_OPTIONS');
    }

    function getSalary($employee_type_id, $function_title_id = '', $experience_in_months = '')
    {
        try {
            $employeeType = $this->employeeTypeService->model::findOrFail($employee_type_id);
            $salary_type = $employeeType->salary_type;

            $minimumSalary = 0;
            $salaryTypeLabel = null;

            if (!empty($salary_type) && array_key_exists($salary_type, config('constants.SALARY_TYPES'))) {
                // Retrieve the FunctionTitle based on its ID
                $functionTitle = FunctionTitle::findOrFail($function_title_id);
                $functionCategory = $functionTitle->functionCategory;

                if ($functionCategory && $functionCategory->sector) {
                    $sectorSalarySteps = $functionCategory->sector->salaryConfig->salarySteps()
                        ->where('from', '<=', $experience_in_months)
                        ->where('to', '>=', $experience_in_months)
                        ->get();

                    if ($sectorSalarySteps->isNotEmpty()) {
                        $function_category_number = $functionCategory->category;

                        if ($salary_type === 'min') {
                            $function_category_number = $function_category_number;
                        } elseif ($salary_type === 'min1') {
                            $function_category_number -= 1;
                        } elseif ($salary_type === 'min2') {
                            $function_category_number -= 2;
                        } elseif ($salary_type === 'flex') {
                            $function_category_number = 999;
                        }

                        $function_category_number = max(1, $function_category_number);

                        $minimumSalaries = $sectorSalarySteps->first()->minimumSalary
                            ->where('category_number', $function_category_number);

                        if ($minimumSalaries->isNotEmpty()) {
                            $minimumSalary = $minimumSalaries->first()->salary;
                        }
                    }
                }
            }

            return [
                'minimumSalary' => $minimumSalary,
                'salary_type'   => [
                    'value' => $salary_type,
                    'label' => config('constants.SALARY_TYPES')[$salary_type],
                ],
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    public function updatePersonalDetails()
    {
        return config('constants.EMPLOYEE_SALARY_TYPE_OPTIONS');
    }

    public function checkEmployeeExistsInCompany($company_id, string $socialSecurityNumber)
    {
        return $this->employeeProfileRepository->checkEmployeeExistsInCompany($company_id, $socialSecurityNumber);
    }

    public function getEmployeeContracts($employeeId)
    {
        $employeeProfile = $this->employeeProfileRepository->getEmployeeProfileById($employeeId);
        $employeeContracts = [
            'active'  => [],
            'expired' => []
        ];
        foreach ($employeeProfile->employeeContracts as $employeeContract) {
            $employeeContract->employeeType;
            $employeeContract->longTermEmployeeContract;
            $contractDetails = $this->formatEmployeeContract($employeeContract);

            if ($employeeContract->end_date == null || strtotime($employeeContract->end_date) > strtotime(date('Y-m-d'))) {
                $employeeContracts['active'][] = $contractDetails;
            } else {
                $employeeContracts['expired'][] = $contractDetails;
            }
        }
        return $employeeContracts;
    }

    public function formatEmployeeContract($employeeContract)
    {
        $contractDetails = [
            'id'            => $employeeContract->id,
            'start_date'    => $employeeContract->start_date,
            'end_date'      => $employeeContract->end_date,
            'employee_type' => $employeeContract->employeeType->name,
            'long_term'     => false,
        ];
        if ($employeeContract->longTermEmployeeContract->exists()) {
            $contractDetails['long_term'] = true;
            $longTermEmployeeContract = $employeeContract->longTermEmployeeContract;
            $contractDetails['sub_type'] = config('constants.SUB_TYPE_OPTIONS')[$longTermEmployeeContract->sub_type];
            $contractDetails['schedule_type'] = config('constants.SCHEDULE_TYPE_OPTIONS')[$longTermEmployeeContract->schedule_type];
            $contractDetails['employment_type'] = config('constants.EMPLOYMENT_TYPE_OPTIONS')[$longTermEmployeeContract->employment_type];
            $contractDetails['weekly_contract_hours'] = $longTermEmployeeContract->weekly_contract_hours;
            $contractDetails['work_days_per_week'] = $longTermEmployeeContract->work_days_per_week;
        }
        return $contractDetails;
    }

    public function getResponsibleCompaniesForUser($user)
    {
        $companies = [];
        if ($user->hasPermissionTo('Access all companies')) {
            $companies = $this->companyService->getActiveCompanies();
        } else {
            $companyUsers = CompanyUser::where('user_id', $user->id)->get();
            foreach ($companyUsers as $companyUser) {
                if ($companyUser->hasPermissionTo('Access company')) {
                    $companies[] = $companyUser->company;
                }
            }
        }
        return $companies;
    }
}
