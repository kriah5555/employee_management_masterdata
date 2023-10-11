<?php

namespace App\Services\Employee;

use App\Models\Company;
use App\Models\Employee\EmployeeContractDetails;
use App\Models\Employee\EmployeeProfile;
use App\Models\Employee\LongTermEmployeeContractDetails;
use App\Models\EmployeeType\EmployeeType;
use App\Models\MealVoucher;
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
use App\Models\Employee\CommuteType;
use App\Repositories\ExtraBenefitsRepository;
use App\Repositories\LocationRepository;

class EmployeeService
{
    protected $employeeProfileRepository;

    protected $addressRepository;

    protected $bankAccountRepository;

    protected $employeeTypeService;

    protected $companyService;

    protected $extraBenefitsRepository;

    protected $locationRepository;

    public function __construct(
        EmployeeProfileRepository $employeeProfileRepository,
        AddressRepository $addressRepository,
        BankAccountRepository $bankAccountRepository,
        EmployeeTypeService $employeeTypeService,
        CompanyService $companyService,
        ExtraBenefitsRepository $extraBenefitsRepository,
        LocationRepository $locationRepository
    ) {
        $this->employeeProfileRepository = $employeeProfileRepository;
        $this->addressRepository = $addressRepository;
        $this->bankAccountRepository = $bankAccountRepository;
        $this->employeeTypeService = $employeeTypeService;
        $this->companyService = $companyService;
        $this->extraBenefitsRepository = $extraBenefitsRepository;
        $this->locationRepository = $locationRepository;
    }
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

    public function createNewEmployee($values, $company_id)
    {
        print_r('as');
        exit;
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
            if (array_key_exists('social_secretory_number', $values) || array_key_exists('contract_number', $values)) {
                $bankAccount = $this->bankAccountRepository->createEmployeeSocialSecretaryDetails($values);
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
        $options['commute_type_options'] = $this->getCommuteTypeOptions();
        $options['employee_contract_options'] = $this->companyService->getEmployeeContractOptionsForCreation($companyId);
        $companyLocations = $this->locationRepository->getCompanyLocations($companyId);
        $options['locations'] = collectionToValueLabelFormat($companyLocations, 'id', 'location_name');
        $options['sub_types'] = $this->getSubTypeOptions();
        $options['schedule_types'] = $this->getScheduleTypeOptions();
        $options['employement_types'] = $this->getEmployementTypeOptions();
        $options['meal_voucher_options'] = $this->getMealVoucherOptions();
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
        return getValueLabelOptionsFromConfig('constants.LANGUAGE_OPTIONS');
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
        return getValueLabelOptionsFromConfig('constants.DEPENDENT_SPOUSE_OPTIONS');
    }

    public function getSubTypeOptions()
    {
        return getKeyNameOptionsFromConfig('constants.SUB_TYPE_OPTIONS');
    }

    public function getScheduleTypeOptions()
    {
        return getKeyNameOptionsFromConfig('constants.SCHEDULE_TYPE_OPTIONS');
    }

    public function getEmployementTypeOptions()
    {
        return getKeyNameOptionsFromConfig('constants.EMPLOYMENT_TYPE_OPTIONS');
    }
}