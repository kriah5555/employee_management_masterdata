<?php

namespace App\Services\Employee;

use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Employee\EmployeeProfile;
use App\Models\Company\Employee\LongTermEmployeeContract;
use App\Models\EmployeeType\EmployeeType;
use App\Models\User\CompanyUser;
use App\Models\EmployeeFunction\FunctionTitle;
use App\Repositories\Employee\EmployeeFunctionDetailsRepository;
use App\Services\CompanyService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\Employee\EmployeeProfileRepository;
use App\Models\User\User;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Repositories\Employee\EmployeeSocialSecretaryDetailsRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\Email\MailService;
use App\Services\Employee\EmployeeContractService;
use App\Services\Employee\EmployeeBenefitService;
use App\Services\Employee\EmployeeCommuteService;

class EmployeeService
{

    public function __construct(
        protected UserService $userService,
        protected MailService $mailService,
        protected CompanyService $companyService,
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected EmployeeFunctionDetailsRepository $employeeFunctionDetailsRepository,
    ) {
    }
    /**
     * Function to get all the employee types
     */

    public function getEmployeeOptions()
    {
        try {
            return ['employees' => $this->employeeProfileRepository->getEmployeeOptions()]; 
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
        return $employeeDetails;
    }

    public function getEmployeeDetails(string $employeeProfileId)
    {
        return $this->formatEmployeeData($this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId, [
            'user',
            'user.userBasicDetails',
            'user.userBasicDetails.gender',
            'user.userContactDetails',
            'user.userFamilyDetails',
            'user.userBankAccount',
            'employeeSocialSecretaryDetails'
        ]));
    }

    public function formatEmployeeData($employee)
    {
        $userBasicDetails = [
            "first_name"             => $employee->user->userBasicDetails->first_name,
            "last_name"              => $employee->user->userBasicDetails->last_name,
            "nationality"            => $employee->user->userBasicDetails->nationality,
            "date_of_birth"          => $employee->user->userBasicDetails->date_of_birth ? date('d-m-Y', strtotime($employee->user->userBasicDetails->date_of_birth)) : null,
            "place_of_birth"         => $employee->user->userBasicDetails->place_of_birth,
            "license_expiry_date"    => $employee->user->userBasicDetails->license_expiry_date ? date('d-m-Y', strtotime($employee->user->userBasicDetails->license_expiry_date)) : null,
            "extra_info"             => $employee->user->userBasicDetails->extra_info,
            "social_security_number" => $employee->user->social_security_number,
            "gender"                 => $employee->user->userBasicDetails->gender,
            "street_house_no"        => $employee->user->userAddress ? $employee->user->userAddress->street_house_no : null,
            "postal_code"            => $employee->user->userAddress ? $employee->user->userAddress->postal_code : null,
            "city"                   => $employee->user->userAddress ? $employee->user->userAddress->city : null,
            "country"                => $employee->user->userAddress ? $employee->user->userAddress->country : null,
            "latitude"               => $employee->user->userAddress ? $employee->user->userAddress->latitude : null,
            "longitude"              => $employee->user->userAddress ? $employee->user->userAddress->longitude : null,
            "email"                  => $employee->user->userContactDetails ? $employee->user->userContactDetails->email : null,
            "phone_number"           => $employee->user->userContactDetails ? $employee->user->userContactDetails->phone_number : null,
            "account_number"         => $employee->user->userBankAccount ? $employee->user->userBankAccount->account_number : null,
            "children"               => $employee->user->userFamilyDetails->children,
        ];
        if ($employee->user->userBasicDetails->language) {
            $userBasicDetails['language'] = [
                'id'   => $employee->user->userBasicDetails->language,
                'name' => config('constants.LANGUAGE_OPTIONS')[$employee->user->userBasicDetails->language]
            ];
        } else {
            $userBasicDetails['language'] = null;
        }
        if ($employee->user->userFamilyDetails->dependent_spouse) {
            $userBasicDetails['dependent_spouse'] = [
                'id'   => $employee->user->userFamilyDetails->dependent_spouse,
                'name' => $employee->user->userFamilyDetails->dependent_spouse ? config('constants.DEPENDENT_SPOUSE_OPTIONS')[$employee->user->userFamilyDetails->dependent_spouse] : null
            ];
        } else {
            $userBasicDetails['dependent_spouse'] = null;
        }
        if ($employee->user->userFamilyDetails->maritalStatus) {
            $userBasicDetails['marital_status'] = [
                'id'   => $employee->user->userFamilyDetails->maritalStatus->id,
                'name' => $employee->user->userFamilyDetails->maritalStatus->name
            ];
        } else {
            $userBasicDetails['marital_status'] = null;
        }
        return $userBasicDetails;
    }


    public function createNewEmployee($values, $company_id)
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();
            DB::connection('tenant')->beginTransaction();
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
            app(EmployeeContractService::class)->createEmployeeContract($values, $employeeProfile->id);
            app(EmployeeBenefitService::class)->createEmployeeBenefits($values, $employeeProfile->id);
            app(EmployeeCommuteService::class)->createEmployeeCommuteDetails($values, $employeeProfile->id);

            DB::connection('master')->commit();
            DB::connection('userdb')->commit();
            DB::connection('tenant')->commit();
            // $this->mailService->sendEmployeeCreationMail($employeeProfile->id);

            return $employeeProfile;
        } catch (Exception $e) {
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
            return $user;
        } catch (Exception $e) {
            DB::connection('master')->rollback();
            DB::connection('userdb')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateEmployee($values, $company_id)
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();

            $existingEmpProfile = $this->userService->getUserById($values['user_id']);

            if ($existingEmpProfile) {
                $user = $this->userService->updateUser($values);

                // $this->mailService->sendEmployeeAccountUpdateMail($values);


            } else {
                $user = $existingEmpProfile->last();
            }

            // Commit transactions
            DB::connection('master')->commit();
            DB::connection('userdb')->commit();

            return $user;



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
        return app(EmployeeSocialSecretaryDetailsRepository::class)->createEmployeeSocialSecretaryDetails($values);
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

    function getSalary($employee_type_id, $function_title_id = '', $experience_in_months = '', $employee_subtype = '') # get salary options to create employee
    {
        try {
            $employeeType = app(EmployeeTypeService::class)->getEmployeeTypeDetails($employee_type_id);
            $salary_type = $employeeType->salary_type['value'];
            # for all employee types hourly salary will be returned, 1 => if teh employee type has long term contract with servant sub type then monthly salary will be returned
            $return_salary_type = ($employeeType->employeeTypeCategory->id == config('constants.LONG_TERM_CONTRACT_ID') && $employee_subtype == 'servant') ? 'monthly_minimum_salary' : 'hourly_minimum_salary';

            $minimumSalary = 0;
            if (!empty($salary_type) && array_key_exists($salary_type, config('constants.SALARY_TYPES'))) {
                // Retrieve the FunctionTitle based on its

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

                        $function_category_number = max(1, $function_category_number); # get group function category

                        $minimumSalaries = $sectorSalarySteps->first()->minimumSalary
                            ->where('category_number', $function_category_number);

                        if ($minimumSalaries->isNotEmpty()) {
                            $minimumSalary = $minimumSalaries->first()->$return_salary_type;
                        }
                    }
                }
            }

            return [
                'minimumSalary' => formatToEuropeCurrency($minimumSalary),
                'salary'        => ucwords(str_replace("_", " ", $return_salary_type)),
                'salary_type'   => [
                    'value' => $salary_type,
                    'label' => $salary_type ? config('constants.SALARY_TYPES')[$salary_type] : null,
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
        if ($user->is_admin || $user->is_moderator) {
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

    public function update($employeeProfileId, $values)
    {
        try {
            $employeeProfile = $this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId);
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();
            $this->userService->updateUserDetails($employeeProfile->user, $values);
            $employeeProfile->employeeSocialSecretaryDetails->update($values);
            // Commit transactions
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

    public function getUserDetails()
    {
        try {
            $userID = Auth::guard('web')->user()->id;
            return $this->userService->getUserDetails($userID);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeDetailsPlanning(array $employeeProfileId)
    {
        return $this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId, [
            'user',
            'user.userBasicDetails',
            'user.userContactDetails',
            'user.userFamilyDetails',
            'user.userBankAccount',
            'employeeSocialSecretaryDetails'
        ]);
    }

    public function getEmployeeActiveTypesByDate($employeeId, $date)
    {
        $activeFunctions = $activeTypes = [];
        $activeContracts = EmployeeContract::with(['employeeType', 'employeeFunctionDetails.functionTitle'])->where('employee_profile_id', $employeeId)->where(function ($query) use ($date) {
            $query->where(function ($query) use ($date) {
                $query->where('start_date', '<', $date)
                    ->where(function ($query) use ($date) {
                        $query->where('end_date', '>', $date)
                            ->orWhereNull('end_date');
                    });
            });
        })->get();
        foreach ($activeContracts as $activeContract) {
            $activeTypes[$activeContract->employeeType->id] = [
                'value' => $activeContract->employeeType->id,
                'label' => $activeContract->employeeType->name,
            ];
            foreach ($activeContract->employeeFunctionDetails as $employeeFunctionDetails) {
                $activeFunctions[$activeContract->employeeType->id][$employeeFunctionDetails->functionTitle->id] = [
                    'value' => $employeeFunctionDetails->functionTitle->id,
                    'label' => $employeeFunctionDetails->functionTitle->name
                ];
            }
        }
        foreach ($activeFunctions as $key => $activeFunction) {
            $activeFunctions[$key] = array_values($activeFunctions[$key]);
        }
        return [
            'employee_types' => array_values($activeTypes),
            'functions'      => $activeFunctions,
        ];
    }
}
