<?php

namespace App\Services\Employee;

use App\Models\Company;
use App\Services\CompanyService;
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
use App\Models\Employee\Transport;
use App\Repositories\ExtraBenefitsRepository;
use App\Repositories\LocationRepository;

use App\Models\EmployeeFunction\FunctionTitle;
use App\Models\Sector\SectorSalarySteps;


class EmployeeService
{

    public function __construct(
        protected EmployeeProfileRepository $employeeProfileRepository,
        protected AddressRepository $addressRepository,
        protected BankAccountRepository $bankAccountRepository,
        protected EmployeeTypeService $employeeTypeService,
        protected CompanyService $companyService,
        protected ExtraBenefitsRepository $extraBenefitsRepository,
        protected LocationRepository $locationRepository
    ) {}
    /**
     * Function to get all the employee types
     */
    public function index(string $companyId)
    {
        return $this->employeeProfileRepository->getAllEmployeeProfilesByCompany($companyId);
    }

    public function show(string $employeeProfileId)
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

    public function createNewEmployeeProfile($values, $company_id)
    {
        try {
            $existingEmpProfile = $this->employeeProfileRepository->getEmployeeProfileBySsn($values['social_security_number']);
            if ($existingEmpProfile->isEmpty()) {
                $uid = $this->createUser($values['first_name'], $values['first_name']);
            } else {
                $uid = $existingEmpProfile->last()->uid;
            }
            DB::beginTransaction();
            $user = User::find($uid);
            $values['uid'] = $uid;
            $address = $this->addressRepository->createAddress($values);
            $values['address_id'] = $address->id;
            $extraBenefits = $this->extraBenefitsRepository->createExtraBenefits($values);
            $values['extra_benefits_id'] = $extraBenefits->id;
            if (array_key_exists('bank_account_number', $values)) {
                $bankAccount = $this->bankAccountRepository->createBankAccount($values);
                $values['bank_accountid'] = $bankAccount->id;
            }
            $values['company_id'] = $company_id;
            $empProfile = $this->employeeProfileRepository->createEmployeeProfile($values);
            $user->assignRole('employee');
            DB::commit();
            return $empProfile;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function createUser($firstName, $lastName)
    {
        $username = $firstName . $lastName;
        $username = strtolower(str_replace(' ', '', $username));
        // $password = generateRandomPassword();
        $password = ucfirst($username) . '$';
        $values = [
            'username' => generateUniqueUsername($username),
            'password' => $password
        ];
        $authorization = request()->header('authorization');
        $bearerToken = substr($authorization, 7);
        $headers = [
            'Authorization' => 'Bearer ' . $bearerToken,
            'Accept'        => 'application/json',
        ];
        $response = microserviceRequest(
            '/service/identity-manager/create-user',
            'POST',
            $values,
            $headers
        );
        if ($response['success']) {
            return $response['data']['id'];
        } else {
            throw new Exception("Error in creating user");
        }
    }

    public function create($companyId)
    {
        $options = [];
        $options['genders'] = $this->getGenderOptions();
        $options['marital_statuses'] = $this->getMaritalStatusOptions();
        $options['languages'] = $this->getLanguageOptions();
        $options['transport_options'] = $this->getTransportOptions();
        $options['employee_type_categories'] = $this->companyService->getEmployeeContractOptionsForCreation($companyId);
        $options['dependent_spouse_options'] = $this->getDependentSpouseOptions();
        $companyLocations = $this->locationRepository->getCompanyLocations($companyId);
        $options['locations'] = collectionToValueLabelFormat($companyLocations, 'id', 'location_name');
        $options['sub_types'] = $this->getSubTypeOptions();
        $options['schedule_types'] = $this->getScheduleTypeOptions();
        $options['employement_types'] = $this->getEmployementTypeOptions();
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
        return getOptionsFromConfig('constants.LANGUAGE_OPTIONS');
    }

    public function getTransportOptions()
    {
        return Transport::where('status', '=', true)->select(['id as value', 'name as label'])->get();
    }

    public function getDependentSpouseOptions()
    {
        return getOptionsFromConfig('constants.DEPENDENT_SPOUSE_OPTIONS');
    }

    public function getSubTypeOptions()
    {
        return getOptionsFromConfig('constants.SUB_TYPE_OPTIONS');
    }

    public function getScheduleTypeOptions()
    {
        return getOptionsFromConfig('constants.SCHEDULE_TYPE_OPTIONS');
    }

    public function getEmployementTypeOptions()
    {
        return getOptionsFromConfig('constants.EMPLOYMENT_TYPE_OPTIONS');
    }

    function getSalary($employee_type_id, $function_title_id = '', $experience_in_months = '')
    {
        try{
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
                'salary_type' => [
                    'value' => $salary_type,
                    'label' => config('constants.SALARY_TYPES')[$salary_type],
                ],
            ];
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        } 
    }
}