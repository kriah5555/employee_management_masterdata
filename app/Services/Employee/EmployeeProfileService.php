<?php

namespace App\Services\Employee;

use Illuminate\Support\Facades\DB;
use Exception;
use App\Repositories\EmployeeProfileRepository;
use App\Repositories\AddressRepository;
use App\Repositories\BankAccountRepository;
use App\Models\User;
use App\Models\Employee\Gender;
use App\Models\Employee\MaritalStatus;

class EmployeeProfileService
{
    protected $employeeProfileRepository;

    protected $addressRepository;
    protected $bankAccountRepository;

    public function __construct(
        EmployeeProfileRepository $employeeProfileRepository,
        AddressRepository $addressRepository,
        BankAccountRepository $bankAccountRepository
    ) {
        $this->employeeProfileRepository = $employeeProfileRepository;
        $this->addressRepository = $addressRepository;
        $this->bankAccountRepository = $bankAccountRepository;
    }
    /**
     * Function to get all the employee types
     */
    public function index(string $companyId)
    {
        return $this->employeeProfileRepository->getAllEmployeeProfilesByCompany($companyId);
    }

    public function createNewEmployeeProfile($values)
    {
        try {
            DB::beginTransaction();
            $existingEmpProfile = $this->employeeProfileRepository->getEmployeeProfileBySsn($values['social_security_number']);
            if ($existingEmpProfile->isEmpty()) {
                $uid = $this->createUser($values['first_name'], $values['first_name']);
            } else {
                $uid = $existingEmpProfile->last()->uid;
            }
            $user = User::find($uid);
            $values['uid'] = $uid;
            $address = $this->addressRepository->createAddress($values['address']);
            $values['address_id'] = $address->id;
            if (array_key_exists('bank_account_number', $values)) {
                $bankAccount = $this->bankAccountRepository->createBankAccount($values);
                $values['bank_accountid'] = $bankAccount->id;
            }
            $values['company_id'] = request()->route('company_id');
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

    public function create()
    {
        $data = [];
        $data['genders'] = $this->getGenderOptions();
        $data['marital_status'] = $this->getMaritalStatusOptions();
        $data['languages'] = $this->getLanguageOptions();
        return $data;
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
        $languages = config('constants.LANGUAGE_OPTIONS');
        return array_map(function ($value, $label) {
            return [
                'value' => $value,
                'label' => $label,
            ];
        }, array_keys($languages), $languages);
    }

}
