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
        $options['commute_type_options'] = $this->getCommuteTypeOptions();
        $options['employee_contract_options'] = $this->companyService->getEmployeeContractOptionsForCreation($companyId);
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
        return getValueLabelOptionsFromConfig('constants.LANGUAGE_OPTIONS');
    }

    public function getCommuteTypeOptions()
    {
        return CommuteType::where('status', '=', true)->select(['id as value', 'name as label'])->get();
    }

    public function getDependentSpouseOptions()
    {
        return getKeyNameOptionsFromConfig('constants.DEPENDENT_SPOUSE_OPTIONS');
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