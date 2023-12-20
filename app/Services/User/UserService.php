<?php

namespace App\Services\User;

use App\Models\User\User;
use App\Models\User\CompanyUser;
use App\Services\Email\MailService;
use App\Models\User\UserBankAccount;
use Illuminate\Support\Facades\Hash;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserAddressRepository;
use App\Repositories\User\UserBankAccountRepository;
use App\Repositories\User\UserBasicDetailsRepository;
use App\Repositories\User\UserFamilyDetailsRepository;
use App\Repositories\User\UserContactDetailsRepository;

class UserService
{

    protected $userRepository;
    protected $userAddressRepository;
    protected $userBankAccountRepository;
    protected $userBasicDetailsRepository;
    protected $userContactDetailsRepository;
    protected $userFamilyDetailsRepository;
    protected $mailService;



    public function __construct(
        UserRepository $userRepository,
        UserBasicDetailsRepository $userBasicDetailsRepository,
        UserAddressRepository $userAddressRepository,
        UserBankAccountRepository $userBankAccountRepository,
        UserContactDetailsRepository $userContactDetailsRepository,
        UserFamilyDetailsRepository $userFamilyDetailsRepository,
        MailService $mailService

    ) {
        $this->userRepository = $userRepository;
        $this->userBasicDetailsRepository = $userBasicDetailsRepository;
        $this->userAddressRepository = $userAddressRepository;
        $this->userBankAccountRepository = $userBankAccountRepository;
        $this->userContactDetailsRepository = $userContactDetailsRepository;
        $this->userFamilyDetailsRepository = $userFamilyDetailsRepository;
        $this->mailService = $mailService;
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
        $password = ucfirst($values['first_name']) . date('dmY', strtotime($values['date_of_birth']));
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
        $this->createUserBankAccount($user, $values);
        return $user;
    }

    public function getUserById($id)
    {
        return $this->userRepository->getUserById($id);
    }


    public function getUserDeviceTokens($id)
    {
        // Retrieve the user by ID
        $user = User::find($id);

        if (!$user) {
            // Handle the case where the user with the given ID is not found
            return null;
        }

        // Access the deviceToken relationship to get the associated device tokens
        $deviceTokens = $user->deviceToken;

        // Extract only the "device_token" values from the array
        $deviceTokensArray = $deviceTokens->pluck('device_token')->toArray();

        // Return the device tokens or an empty array if not found
        return $deviceTokensArray;
    }



    public function updateUserBankAccount(User $user, $values)
    {
        $existingAccountNumber = '';
        $values['user_id'] = $user->id;
        $UserBankObject = $user->userBankAccount;

        // if ($UserBankObject)
        $newAccountNumber = $values['account_number'];
        if (is_null($UserBankObject)) {
            $UserBankObject = $this->createUserBankAccount($user, $values);
        } else {
            $existingAccountNumber = $UserBankObject->account_number;
            $details = $this->userBankAccountRepository->updateUserBankAccount($UserBankObject, $values);
        }

        if ($newAccountNumber != $existingAccountNumber) {
            $this->mailService->sendEmployeeAccountUpdateMail($values);
        }


    }

    public function updateUserBasicDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;
        $values['date_of_birth'] = date('Y-m-d', strtotime($values['date_of_birth']));
        $userDetailsObject = $user->userBasicDetailsById($user->id)->get()->first();
        if (is_null($userDetailsObject)) {
            $userBasicDetails = $this->createUserBasicDetails($user, $values);
        } else {
            return $this->userBasicDetailsRepository->updateUserBasicDetails($userDetailsObject, $values);
        }
    }


    public function updateUserAddress(User $user, $values)
    {
        $values['user_id'] = $user->id;

        $userAddressObject = $user->userAddressById($user->id)->get()->first();

        if (is_null($userAddressObject)) {
            $userAddress = $this->createUserAddress($user, $values);
        } else {
            return $this->userAddressRepository->updateUserAddress($userAddressObject, $values);
        }
    }

    public function updateContactDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;

        $userContactObject = $user->userContactById($user->id)->get()->first();
        if (is_null($userContactObject)) {
            $userAddress = $this->createUserContactDetails($user, $values);
        } else {

            return $this->userContactDetailsRepository->updateUserContactDetails($userContactObject, $values);
        }
    }

    public function updateUser($values)
    {
        $saveValues = [
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

    public function updateUserDetails(User $user, $values)
    {
        $this->userBasicDetailsRepository->updateUserBasicDetails($user->userBasicDetails, $values);
        $this->userAddressRepository->updateUserAddress($user->userAddress, $values);
        $this->userContactDetailsRepository->updateUserContactDetails($user->userContactDetails, $values);
        $this->userFamilyDetailsRepository->updateUserFamilyDetails($user->userFamilyDetails, $values);
        $this->userBankAccountRepository->updateUserBankAccount($user->userBankAccount, $values);
    }

    public function getUserDetails($userID)
    {
        $user = User::find($userID);

        if (!$user) {
            return ['error' => 'User not found'];
        }

        $userBasicDetails = $user->userBasicDetailsById($user->id)->get()->first();
        $userAddressDetails = $user->userAddressById($user->id)->get()->first();
        $userContactDetails = $user->userContactById($user->id)->get()->first();
        $userBankAccountDetails = $user->userBankDetails($user->id)->get()->first();

        // Check if each detail is not null before calling toApiResponseFormat()
        $userBasicDetails = $userBasicDetails ? $userBasicDetails->toApiResponseFormat() : null;
        $userAddressDetails = $userAddressDetails ? $userAddressDetails->toApiResponseFormat() : null;
        $userContactDetails = $userContactDetails ? $userContactDetails->toApiResponseFormat() : null;
        $userBankAccountDetails = $userBankAccountDetails ? $userBankAccountDetails->toApiResponseFormat() : null;

        return array_merge(
            $user->toArray(),
            $userBasicDetails ?: [],
            $userAddressDetails ?: [],
            $userContactDetails ?: [],
            $userBankAccountDetails ?: []
        );
    }



}
