<?php

namespace App\Services\Employee;

use App\Models\Company\Employee\EmployeeContract;
use App\Models\Company\Employee\EmployeeProfile;
use App\Models\Company\Employee\LongTermEmployeeContract;
use App\Models\EmployeeType\EmployeeType;
use App\Models\Planning\PlanningBase;
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
use App\Models\Company\Company;
use App\Services\Configuration\FlexSalaryService;

class EmployeeService
{

    public function __construct(
        protected UserService $userService,
        protected MailService $mailService,
        protected CompanyService $companyService,
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected EmployeeFunctionDetailsRepository $employeeFunctionDetailsRepository,
        protected FlexSalaryService $flexSalaryService,
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
        $noContractEmployees = [];
        foreach ($employees as $employee) {
            $employee->user;
            $employee->user->userBasicDetails;
            $employee->user->UserContactDetails;
            $currentDate = Carbon::now();

            $employeeContracts = $employee->employeeContracts->filter(function ($employeeContract) use ($currentDate) {
                return Carbon::parse($employeeContract->start_date)->lessThanOrEqualTo($currentDate) &&
                    (is_null($employeeContract->start_date) || Carbon::parse($employeeContract->end_date)->greaterThanOrEqualTo($currentDate));
            });
            if ($employeeContracts->isNotEmpty()) {
                $currentContract = $employeeContracts->first();
                if (!array_key_exists($currentContract->employeeType->id, $response)) {
                    $response[$currentContract->employeeType->id] = [
                        'employee_type' => $currentContract->employeeType->name,
                        'employees'     => []
                    ];
                }
                $employee->profile_picture_url = $employee->user->userProfilePicture ? $employee->user->userProfilePicture->profile_picture_url : null;
                $response[$currentContract->employeeType->id]['employees'][] = $employee;
            } else {
                $noContractEmployees[] = $employee;
            }
        }
        if (count($noContractEmployees)) {
            $response[999] = [
                'employee_type' => 'No contracts',
                'employees'     => $noContractEmployees
            ];
        }
        return array_values($response);
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
        $employee_basic_details = $employee->user->userBasicDetails;
        $userBasicDetails = [
            "username"                => $employee->user->username,
            "responsible_person_id"   => $employee->responsible_person_id,
            "responsible_person_name" => ($employee->responsiblePerson) ? $employee->responsiblePerson->full_name : null,
            "first_name"              => $employee_basic_details ? $employee->user->userBasicDetails->first_name : null,
            "last_name"               => $employee_basic_details ? $employee->user->userBasicDetails->last_name : null,
            "nationality"             => $employee_basic_details ? $employee->user->userBasicDetails->nationality : null,
            "date_of_birth"           => $employee_basic_details ? $employee->user->userBasicDetails->date_of_birth ? date('d-m-Y', strtotime($employee->user->userBasicDetails->date_of_birth)) : null : null,
            "place_of_birth"          => $employee_basic_details ? $employee->user->userBasicDetails->place_of_birth : null,
            "license_expiry_date"     => $employee_basic_details ? $employee->user->userBasicDetails->license_expiry_date ? date('d-m-Y', strtotime($employee->user->userBasicDetails->license_expiry_date)) : null : null,
            "extra_info"              => $employee_basic_details ? $employee->user->userBasicDetails->extra_info : null,
            "social_security_number"  => $employee->user->social_security_number,
            "gender"                  => $employee_basic_details ? $employee->user->userBasicDetails->gender : null,
            "street_house_no"         => $employee->user->userAddress ? $employee->user->userAddress->street_house_no : null,
            "postal_code"             => $employee->user->userAddress ? $employee->user->userAddress->postal_code : null,
            "city"                    => $employee->user->userAddress ? $employee->user->userAddress->city : null,
            "country"                 => $employee->user->userAddress ? $employee->user->userAddress->country : null,
            "latitude"                => $employee->user->userAddress ? $employee->user->userAddress->latitude : null,
            "longitude"               => $employee->user->userAddress ? $employee->user->userAddress->longitude : null,
            "email"                   => $employee->user->userContactDetails ? $employee->user->userContactDetails->email : null,
            "phone_number"            => $employee->user->userContactDetails ? $employee->user->userContactDetails->phone_number : null,
            "account_number"          => $employee->user->userBankAccount ? $employee->user->userBankAccount->account_number : null,
            "children"                => $employee->user->userFamilyDetails->children,
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
            $new_user = true;
            if ($existingEmpProfile->isEmpty()) {
                $user = $this->userService->createNewUser($values);
            } else {
                $user = $existingEmpProfile->last();
                $new_user = false;
            }
            $this->createCompanyUser($user, $company_id, 'employee');
            $employeeProfile = $this->createEmployeeProfile($user, $values);
            $this->createEmployeeSocialSecretaryDetails($employeeProfile, $values);
            app(EmployeeContractService::class)->createEmployeeContract($values, $employeeProfile->id);
            app(EmployeeBenefitService::class)->createEmployeeBenefits($values, $employeeProfile->id);
            app(EmployeeCommuteService::class)->createEmployeeCommuteDetails($values, $employeeProfile->id);


            DB::connection('master')->commit();
            DB::connection('userdb')->commit();
            DB::connection('tenant')->commit();

            $password = ucfirst($values['first_name']) . date('dmY', strtotime($values['date_of_birth']));
            $this->mailService->sendEmployeeCreationMail($employeeProfile->id, $new_user, $values['language'], $password);

            return $employeeProfile;
        } catch (Exception $e) {
            DB::connection('master')->rollback();
            DB::connection('userdb')->rollback();
            DB::connection('tenant')->rollback();
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



    public function updateEmployeePersonal($values, $company_id)
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();

            $existingEmpProfile = $this->userService->getUserById($values['user_id']);

            if ($existingEmpProfile) {
                $user = $this->userService->updateEmployeePersonal($values);
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

    public function updateEmployeeAddress($values, $company_id)
    {
        try {
            DB::connection('master')->beginTransaction();
            DB::connection('userdb')->beginTransaction();

            $existingEmpProfile = $this->userService->getUserById($values['user_id']);

            if ($existingEmpProfile) {
                $user = $this->userService->updateEmployeeAddress($values);
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
        $companyUser = CompanyUser::updateOrCreate(
            [
                'user_id'    => $user->id,
                'company_id' => $company_id,
            ],
            []
        );
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

    public function getSalary($employee_type_id, $function_title_id = '', $experience_in_months = '', $employee_subtype = '')
    {
        try {
            $employeeType = app(EmployeeTypeService::class)->getEmployeeTypeDetails($employee_type_id);
            $salary_type = $employeeType->salary_type['value'];
            # for all employee types hourly salary will be returned, 1 => if teh employee type has long term contract with servant sub type then monthly salary will be returned
            $return_salary_type = ($employeeType->employeeTypeCategory->id == config('constants.LONG_TERM_CONTRACT_ID') && $employee_subtype == 'servant') ? 'monthly_minimum_salary' : 'hourly_minimum_salary';

            // Retrieve function title and its category
            $functionTitle = FunctionTitle::findOrFail($function_title_id);
            $functionCategory = $functionTitle->functionCategory;

            // Retrieve sector salary configuration
            if ($functionCategory && $functionCategory->sector) {
                $sectorSalarySteps = $functionCategory->sector->salaryConfig->salarySteps()
                    ->where('from', '<=', $experience_in_months)
                    ->where('to', '>=', $experience_in_months)
                    ->get();

                if ($sectorSalarySteps->isNotEmpty()) {
                    $function_category_number = $functionCategory->category;

                    // Adjust function category number based on salary type
                    if ($salary_type === 'min1') {
                        $function_category_number -= 1;
                    } elseif ($salary_type === 'min2') {
                        $function_category_number -= 2;
                    } elseif ($salary_type === 'flex') {
                        $function_category_number = 999;
                    }

                    if ($function_category_number != 999) {
                        $function_category_number = max(1, $function_category_number); // Get group function category

                        // Retrieve minimum salary based on function category number
                        $minimumSalaries = $sectorSalarySteps->first()->minimumSalary
                            ->where('category_number', $function_category_number);

                        if ($minimumSalaries->isNotEmpty()) {
                            $minimumSalary = $minimumSalaries->first()->$return_salary_type;
                        }
                    } else {
                        // Retrieve flex salary
                        $data = $this->flexSalaryService->getFlexSalaryByKey('flex_min_salary');
                        $minimumSalary = $data['number_format'];
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

    public function getUserDetails($userID)
    {
        try {

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
                $query->where('start_date', '<=', $date)
                    ->where(function ($query) use ($date) {
                        $query->where('end_date', '>=', $date)
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

    public function getEmployeeCompanies($user)
    {
        $companies = [];
        $companyUsers = CompanyUser::where('user_id', $user->id)->get();
        foreach ($companyUsers as $companyUser) {
            if ($companyUser->hasRole('employee')) {
                $companies[] = [
                    'id'   => $companyUser->company->id,
                    'name' => $companyUser->company->company_name,
                ];
            }
        }
        return $companies;
    }

    public function createEmployeeSignature($employee_profile_id, $details)
    {
        try {
            DB::connection('tenant')->beginTransaction();
            $employee_profile = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id);
            if ($employee_profile->signature) {
                $employee_profile->signature->delete();
            }
            $employee_profile->signature()->create($details);
            DB::connection('tenant')->commit();
            return $employee_profile;
        } catch (Exception $e) {
            DB::connection('tenant')->rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function getEmployeeSignature($employee_profile_id)
    {
        try {
            return $employee_profile = $this->employeeProfileRepository->getEmployeeProfileById($employee_profile_id)->signature;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function deleteEmployee($employeeProfileId)
    {
        $hasWorked = $this->checkEmployeeHasWorked($employeeProfileId);
        if ($hasWorked) {
            $this->archiveEmployee($employeeProfileId);
        } else {
            $this->deleteEmployeeFromCompany($employeeProfileId);
        }
        // try {
        //     return $this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId)->signature;
        // } catch (Exception $e) {
        //     error_log($e->getMessage());
        //     throw $e;
        // }
    }

    public function checkEmployeeHasWorked($employeeProfileId)
    {
        $plannings = PlanningBase::where('employee_profile_id', $employeeProfileId)->get();
        return count($plannings) ? true : false;
    }

    public function archiveEmployee($employeeProfileId)
    {
        $employeeProfile = EmployeeProfile::findOrFail($employeeProfileId);
    }

    public function deleteEmployeeFromCompany($employeeProfileId)
    {
        DB::connection('tenant')->beginTransaction();
        DB::connection('master')->beginTransaction();
        $employeeProfile = EmployeeProfile::findOrFail($employeeProfileId);
        $employeeProfile->employeeSocialSecretaryDetails->delete();
        foreach ($employeeProfile->employeeContracts as $employeeContract) {
            $employeeContract->delete();
        }
        foreach ($employeeProfile->employeeCommute as $employeeCommute) {
            $employeeCommute->delete();
        }
        CompanyUser::where('company_id', getCompanyId())->where('user_id', $employeeProfile->user_id)->delete();
        $employeeProfile->delete();
        DB::connection('master')->commit();
        DB::connection('tenant')->commit();
    }

    public function getCompanyUserObjectForEmployee($userId, $companyId)
    {
        return CompanyUser::where('user_id', $userId)->where('company_id', $companyId)->get();
    }

    public function getCompanyEmployees()
    {
        $response = [];
        $employees = $this->employeeProfileRepository->getAllEmployeeProfiles([
            'user.userBasicDetails',
        ]);
        $response = [];
        foreach ($employees as $employee) {
            $response[] = [
                'value' => $employee->id,
                'label' => $employee->user->userBasicDetails->first_name . ' ' . $employee->user->userBasicDetails->last_name
            ];
        }

        return sortArrayByKey($response, 'label');
    }
}
