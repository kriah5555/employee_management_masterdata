<?php

namespace App\Services\User;

use App\Repositories\User\UserBankAccountRepository;
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


    public function __construct(UserRepository $userRepository, UserBasicDetailsRepository $userBasicDetailsRepository, UserAddressRepository $userAddressRepository, UserBankAccountRepository $userBankAccountRepository)
    {
        $this->userRepository = $userRepository;
        $this->userBasicDetailsRepository = $userBasicDetailsRepository;
        $this->userAddressRepository = $userAddressRepository;
        $this->userBankAccountRepository = $userBankAccountRepository;
    }

    public function getUserBySocialSecurityNumber($socialSecurityNumber)
    {
        return $this->userRepository->getUserBySocialSecurityNumber($socialSecurityNumber);
    }

    public function createUserBankAccount($values)
    {
        return $this->userBankAccountRepository->createUserBankAccount($values);
    }
    public function createUserBasicDetails(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->userBasicDetailsRepository->createUserBasicDetails($values);
    }
    public function createUserAddress(User $user, $values)
    {
        $values['user_id'] = $user->id;
        return $this->userAddressRepository->createUserAddress($values);
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
        return $user;
    }
}
