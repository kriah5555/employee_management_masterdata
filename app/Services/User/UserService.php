<?php

namespace App\Services\User;

use App\Repositories\User\UserBankAccountRepository;
use App\Repositories\User\UserContactDetailsRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserAddressRepository;
use App\Models\User\User;
use App\Repositories\User\UserBasicDetailsRepository;
use Illuminate\Support\Facades\Hash;


class UserService
{

    protected $userRepository;
    protected $userAddressRepository;
    protected $userBankAccountRepository;
    protected $userBasicDetailsRepository;
    protected $userContactDetailsRepository;


    public function __construct(
        UserRepository $userRepository,
        UserBasicDetailsRepository $userBasicDetailsRepository,
        UserAddressRepository $userAddressRepository,
        UserBankAccountRepository $userBankAccountRepository,
        UserContactDetailsRepository $userContactDetailsRepository
    ) {
        $this->userRepository = $userRepository;
        $this->userBasicDetailsRepository = $userBasicDetailsRepository;
        $this->userAddressRepository = $userAddressRepository;
        $this->userBankAccountRepository = $userBankAccountRepository;
        $this->userContactDetailsRepository = $userContactDetailsRepository;
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
        $values['date_of_birth'] = date('Y-m-d', strtotime($values['date_of_birth']));
        $values['license_expiry_date'] = date('Y-m-d', strtotime($values['license_expiry_date']));

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
        if (array_key_exists('bank_account_number', $values)) {
            $this->createUserBankAccount($user, $values);
        }
        return $user;
    }
}
