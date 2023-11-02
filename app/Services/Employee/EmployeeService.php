<?php

namespace App\Services\Employee;

use App\Models\Company\Company;
use App\Models\Employee\EmployeeContractDetails;
use App\Models\Employee\EmployeeProfile;
use App\Models\Employee\LongTermEmployeeContractDetails;
use App\Models\EmployeeType\EmployeeType;
use App\Models\MealVoucher;
use App\Models\User\UserBasicDetails;
use App\Services\CompanyService;
use App\Services\User\UserService;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\EmployeeProfileRepository;
use App\Repositories\AddressRepository;
use App\Repositories\BankAccountRepository;
use App\Models\User;
use App\Models\Employee\Gender;
use App\Models\Employee\MaritalStatus;
use App\Services\EmployeeType\EmployeeTypeService;
use App\Models\EmployeeType\EmployeeTypeCategory;
use App\Models\Employee\CommuteType;
use App\Repositories\ExtraBenefitsRepository;
use App\Repositories\Company\LocationRepository;

use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Sector\SectorSalarySteps;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;


class EmployeeService
{

    public function __construct(
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected AddressRepository $addressRepository,
        protected BankAccountRepository $bankAccountRepository,
        protected EmployeeTypeService $employeeTypeService,
        protected CompanyService $companyService,
        protected ExtraBenefitsRepository $extraBenefitsRepository,
        protected LocationRepository $locationRepository,
        protected UserService $userService,
    ) {
    }
    /**
     * Function to get all the employee types
     */
    public function index()
    {
        $response = [];
        $employees = $this->employeeProfileRepository->getAllEmployeeProfiles();
        foreach ($employees as $employee) {
            $employee->user;
            $employee->user->userBasicDetails;
            $employee->user_basic_details = UserBasicDetails::where("user_id", $employee->user->id)->first();
            // print_r($employee->user->id);
            // exit;
            print_r(UserBasicDetails::where("user_id", $employee->user->id)->first());
            exit;
            $currentDate = Carbon::now();

            $employeeContracts = $employee->employeeContractDetails->filter(function ($employeeContractDetails) use ($currentDate) {
                // Check if 'to_date' is greater than or equal to today or if it's null
                return (
                    Carbon::parse($employeeContractDetails->start_date)->lessThanOrEqualTo($currentDate) &&
                    (is_null($employeeContractDetails->start_date) || Carbon::parse($employeeContractDetails->end_date)->greaterThanOrEqualTo($currentDate))
                );
            });
            $currentContract = $employeeContracts[0];
            if (!array_key_exists($currentContract->employeeType->id, $response)) {
                $response[$currentContract->employeeType->id] = [
                    'employee_type' => $currentContract->employeeType->name,
                    'employees'     => []
                ];
            }
            $response[$currentContract->employeeType->id]['employees'][] = $employee;
        }
        return array_values($response);
    }

    public function getEmployeeDetails(string $employeeProfileId)
    {
        return $this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId);
    }

    public function edit(string $employeeProfileId)
    {
        $options = $this->create();
        $employeeProfile = $this->employeeProfileRepository->getEmployeeProfileById($employeeProfileId);
        $options['details'] = $employeeProfile;
        return $options;
    }

    public function createNewEmployee($values, $company_id)
    {
        try {
            DB::beginTransaction();
            $existingEmpProfile = $this->userService->getUserBySocialSecurityNumber($values['social_security_number']);
            if ($existingEmpProfile->isEmpty()) {
                $uid = $this->createUser($values);
            } else {
                $uid = $existingEmpProfile->last()->id;
            }
            $user = User::find($uid);
            $values['user_id'] = $uid;
            $address = $this->addressRepository->createAddress($values);
            $values['address_id'] = $address->id;
            $extraBenefits = $this->extraBenefitsRepository->createExtraBenefits($values);
            $values['extra_benefits_id'] = $extraBenefits->id;
            if (array_key_exists('bank_account_number', $values)) {
                $bankAccount = $this->bankAccountRepository->createBankAccount($values);
                $values['bank_account_id'] = $bankAccount->id;
            }
            $values['company_id'] = $company_id;
            $empProfile = $this->employeeProfileRepository->createEmployeeProfile($values);
            if (array_key_exists('social_secretory_number', $values) || array_key_exists('contract_number', $values)) {
                $bankAccount = $this->createEmployeeSocialSecretaryDetails($empProfile, $values);
                $values['bank_accountid'] = $bankAccount->id;
            }
            $this->createEmployeeContract($empProfile, $values['employee_contract_details']);
            $user->assignRole('employee');
            DB::commit();
            return $empProfile;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createEmployeeContract($empProfile, $contractDetails)
    {
        $contractDetails['employee_profile_id'] = $empProfile->id;
        $contractDetails['weekly_contract_hours'] = str_replace(',', '.', $contractDetails['weekly_contract_hours']);
        $employeeType = EmployeeType::findOrFail($contractDetails['employee_type_id']);
        $employeeContractDetails = EmployeeContractDetails::create($contractDetails);
        if ($employeeType->employeeTypeCategory->id == 1) {
            $contractDetails['employee_contract_details_id'] = $employeeContractDetails->id;
            LongTermEmployeeContractDetails::create($contractDetails);
        }
    }

    public function createEmployeeSocialSecretaryDetails($empProfile, $contractDetails)
    {
        $contractDetails['employee_profile_id'] = $empProfile->id;
        $contractDetails['weekly_contract_hours'] = str_replace(',', '.', $contractDetails['weekly_contract_hours']);
        $employeeType = EmployeeType::findOrFail($contractDetails['employee_type_id']);
        $employeeContractDetails = EmployeeContractDetails::create($contractDetails);
        if ($employeeType->employeeTypeCategory->id == 1) {
            $contractDetails['employee_contract_details_id'] = $employeeContractDetails->id;
            LongTermEmployeeContractDetails::create($contractDetails);
        }
    }

    // public function createEmployeeFunction($empProfile, $contractDetails)
    // {
    //     $contractDetails['employee_profile_id'] = $empProfile->id;
    //     $contractDetails['weekly_contract_hours'] = str_replace(',', '.', $contractDetails['weekly_contract_hours']);
    //     $employeeType = EmployeeType::findOrFail($contractDetails['employee_type_id']);
    //     $employeeContractDetails = EmployeeContractDetails::create($contractDetails);
    //     if ($employeeType->employeeTypeCategory->id == 1) {
    //         $contractDetails['employee_contract_details_id'] = $employeeContractDetails->id;
    //         LongTermEmployeeContractDetails::create($contractDetails);
    //     }
    // }

    public function createUser($values)
    {
        $username = $values['first_name'] . $values['last_name'];
        $username = strtolower(str_replace(' ', '', $username));
        // $password = generateRandomPassword();
        $password = ucfirst($username) . '$';
        // $values = [
        //     'username' => generateUniqueUsername($username),
        //     'password' => Hash::make($password)
        // ];
        $values['username'] = generateUniqueUsername($username);
        $values['password'] = Hash::make($password);
        DB::beginTransaction();
        $user = User::create($values);
        $values['user_id'] = $user->id;
        UserBasicDetails::create($values);
        DB::commit();
        return $user->id;
        // $authorization = request()->header('authorization');
        // $bearerToken = substr($authorization, 7);
        // $headers = [
        //     'Authorization' => 'Bearer ' . $bearerToken,
        //     'Accept'        => 'application/json',
        // ];
        // $response = microserviceRequest(
        //     '/service/identity-manager/create-user',
        //     'POST',
        //     $values,
        //     $headers
        // );
        // if ($response['success']) {
        //     return $response['data']['id'];
        // } else {
        //     throw new Exception("Error in creating user");
        // }
    }

    public function create($companyId)
    {
        $options = [];
        $options['commute_type_options'] = $this->getCommuteTypeOptions();
        $options['employee_contract_options'] = $this->companyService->getEmployeeContractOptionsForCreation($companyId);
        $companyLocations = $this->locationRepository->getCompanyLocations($companyId);
        $options['locations'] = collectionToValueLabelFormat($companyLocations, 'id', 'location_name');
        $options['sub_types'] = $this->getSubTypeOptions();
        $options['schedule_types'] = $this->getScheduleTypeOptions();
        $options['meal_voucher_options'] = $this->getMealVoucherOptions();
        $options['employment_types'] = $this->getEmploymentTypeOptions();
        $options['functions'] = $this->companyService->getFunctionOptionsForCompany($this->companyService->getCompanyDetails($companyId));
        return $options;
    }

    public function getEmployeesOptionsForCompany($company_id)
    {

    }

    public function getGenderOptions()
    {
        return Gender::where('status', '=', true)->select(['id as value', 'name as label'])->get();
    }

    public function getMaritalStatusOptions()
    {
        return MaritalStatus::where('status', '=', true)->select(['id as value', 'name as label'])->get();
    }

    public function getLanguageOptions()
    {
        $url = config('app.identity_manager_url') . '/employee/get-language-options';
        $response = makeApiRequest($url, 'GET');
        if ($response['success']) {
            return $response['data'];
        } else {
            throw new Exception('Failed to get language options');
        }
    }

    public function getCommuteTypeOptions()
    {
        return CommuteType::where('status', '=', true)->select(['id as value', 'name as label'])->get();
    }

    public function getMealVoucherOptions()
    {
        return MealVoucher::where('status', '=', true)->select(['id as value', 'name as label'])->get();
    }

    public function getDependentSpouseOptions()
    {
        $url = config('app.identity_manager_url') . '/employee/get-dependent-spouse-options';
        $response = makeApiRequest($url, 'GET');
        if ($response['success']) {
            return $response['data'];
        } else {
            throw new Exception('Failed to get dependent spouse options');
        }
    }

    public function getSubTypeOptions()
    {
        return getValueLabelOptionsFromConfig('constants.SUB_TYPE_OPTIONS');
    }

    public function getScheduleTypeOptions()
    {
        return getValueLabelOptionsFromConfig('constants.SCHEDULE_TYPE_OPTIONS');
    }

    public function getEmploymentTypeOptions()
    {
        return getValueLabelOptionsFromConfig('constants.EMPLOYMENT_TYPE_OPTIONS');
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

    public function getGenders()
    {
        $url = config('app.identity_manager_url') . '/genders';
        $response = makeApiRequest($url, 'GET');
        if ($response['success']) {
            return $response['data'];
        } else {
            throw new Exception('Failed to get genders');
        }
    }

    public function getMaritalStatus()
    {
        $url = config('app.identity_manager_url') . '/marital-statuses';
        $response = makeApiRequest($url, 'GET');
        if ($response['success']) {
            return $response['data'];
        } else {
            throw new Exception('Failed to get marital statuses');
        }
    }
}
