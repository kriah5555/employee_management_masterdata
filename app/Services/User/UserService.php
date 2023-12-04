<?php

namespace App\Services\User;

use App\Repositories\User\UserBankAccountRepository;
use App\Repositories\User\UserContactDetailsRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserAddressRepository;
use App\Models\User\User;
use App\Repositories\User\UserBasicDetailsRepository;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserFamilyDetailsRepository;
use App\Models\User\CompanyUser;

class UserService
{

    protected $userRepository;
    protected $userAddressRepository;
    protected $userBankAccountRepository;
    protected $userBasicDetailsRepository;
    protected $userContactDetailsRepository;
    protected $userFamilyDetailsRepository;


    public function __construct(
        UserRepository $userRepository,
        UserBasicDetailsRepository $userBasicDetailsRepository,
        UserAddressRepository $userAddressRepository,
        UserBankAccountRepository $userBankAccountRepository,
        UserContactDetailsRepository $userContactDetailsRepository,
        UserFamilyDetailsRepository $userFamilyDetailsRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userBasicDetailsRepository = $userBasicDetailsRepository;
        $this->userAddressRepository = $userAddressRepository;
        $this->userBankAccountRepository = $userBankAccountRepository;
        $this->userContactDetailsRepository = $userContactDetailsRepository;
        $this->userFamilyDetailsRepository = $userFamilyDetailsRepository;
    }

    public function getUserBySocialSecurityNumber($socialSecurityNumber)
    {
        return $this->userRepository->getUserBySocialSecurityNumber($socialSecurityNumber);
    }

    public function createUserBankAccount(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->userBankAccountRepository->createUserBankAccount($values);
    }
    public function createUserBasicDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;
        $values['date_of_birth'] = (isset($values['date_of_birth']) && $values['date_of_birth'] != '')
            ? date('Y-m-d', strtotime($values['date_of_birth'])) : null;
        $values['license_expiry_date'] = (isset($values['license_expiry_date']) && $values['license_expiry_date'] != '')
            ? date('Y-m-d', strtotime($values['license_expiry_date'])) : null;
        return $this->userBasicDetailsRepository->createUserBasicDetails($values);
    }
    public function createUserAddress(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->userAddressRepository->createUserAddress($values);
    }
    public function createUserContactDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->userContactDetailsRepository->createUserContactDetails($values);
    }
    public function createUserFamilyDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->userFamilyDetailsRepository->createUserFamilyDetails($values);
    }

    public function createNewUser($values)
    {
        $username = $values['first_name'] . $values['last_name'];
        $username = strtolower(str_replace(' ', '', $username));
        $password = ucfirst($username) . '$';
        $saveValues = [
            'username'               => generateUniqueUsername($username),
            'password'               => Hash::make($password),
            'social_security_number' => $values['social_security_number'],
        ];
        $user = User::create($saveValues);
        $this->createUserBasicDetails($user, $values);
        $this->createUserAddress($user, $values);
        $this->createUserContactDetails($user, $values);
        $this->createUserFamilyDetails($user, $values);
        if (array_key_exists('bank_account_number', $values)) {
            $this->createUserBankAccount($user, $values);
        }
        return $user;
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }

    public function updateUserBankAccount(User $user, $values)
    {
        $values['user_id'] = $user->id;
        $UserBankObject = $user->userBankDetails($user->id)->get()[0];
        return $this->userBankAccountRepository->updateUserBankAccount($UserBankObject ,$values);
    }

    public function updateUserBasicDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;
        $values['date_of_birth'] = date('Y-m-d', strtotime($values['date_of_birth']));
        $userDetailsObject = $user->userBasicDetailsById($user->id)->get()[0];

        return $this->userBasicDetailsRepository->updateUserBasicDetails($userDetailsObject ,$values);
    }


    public function updateUserAddress(User $user, $values)
    {
        $values['user_id'] = $user->id;

        $userAddressObject = $user->userAddressById($user->id)->get()[0];

        return $this->userAddressRepository->updateUserAddress($userAddressObject, $values);
    }

    public function updateContactDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;

        $userContactObject = $user->userContactById($user->id)->get()[0];

        return $this->userContactDetailsRepository->updateUserContactDetails($userContactObject, $values);
    }

    public function updateUser($values)
    {
        $username = $values['first_name'] . $values['last_name'];
        $username = strtolower(str_replace(' ', '', $username));
        $saveValues = [
            'username'               => generateUniqueUsername($username),
            'social_security_number' => $values['social_security_number'],
        ];

        $user = User::findOrFail($values['user_id']);
        $updateUser = $user->update($saveValues);

        $this->updateUserBankAccount($user, $values);
        $this->updateUserBasicDetails($user, $values);
        $this->updateUserAddress($user, $values);
        $this->updateContactDetails($user, $values);


        // If you need to return the updated user:
        // return $user;

        // Otherwise, return the update status:
        return $updateUser;
    }

    public function getCompanyUserRoles($user_id, $company_id)
    {
        $company_user = CompanyUser::where(['company_id' => $company_id, 'user_id' => $user_id])->get()->first();
        return $company_user->roles->pluck('name')->toArray();
    }
}
