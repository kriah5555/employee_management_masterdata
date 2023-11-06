<?php

namespace App\Services\User;

use App\Repositories\User\UserBankAccountRepository;
use App\Repositories\User\UserRepository;
use App\Repositories\User\UserAddressRepository;
use App\Models\User\User;

class UserService
{

    protected $userRepository;
    protected $userAddressRepository;
    protected $userBankAccountRepository;


    public function __construct(UserRepository $userRepository, UserAddressRepository $userAddressRepository, UserBankAccountRepository $userBankAccountRepository)
    {
        $this->userRepository = $userRepository;
        $this->userAddressRepository = $userAddressRepository;
        $this->userBankAccountRepository = $userBankAccountRepository;
    }

    public function getUserBySocialSecurityNumber($socialSecurityNumber)
    {
        return $this->userRepository->getUserBySocialSecurityNumber($socialSecurityNumber);
    }

    public function createUserAddress($values)
    {
        return $this->userAddressRepository->createUserAddress($values);
    }
    public function createUserBankAccount($values)
    {
        return $this->userBankAccountRepository->createUserBankAccount($values);
    }
    public function createOrUpdateUserBasicDetails(User $user, $values)
    {
        $updateValues = [
            'first_name'          => $values['first_name'],
            'last_name'           => $values['last_name'],
            'gender_id'           => $values['gender_id'],
            'date_of_birth'       => $values['date_of_birth'],
            'license_expiry_date' => $values['license_expiry_date'],
            'language'            => $values['language']
        ];
        if (array_key_exists('place_of_birth', $values) && $values['place_of_birth'] != '') {
            $updateValues['place_of_birth'] = $values['place_of_birth'];
        }
        if (array_key_exists('extra_info', $values) && $values['extra_info'] != '') {
            $updateValues['extra_info'] = $values['extra_info'];
        }
        $user->userBasicDetails()->updateOrCreate($updateValues);
    }
    public function createOrUpdateUserAddress(User $user, $values)
    {
        $updateValues = [
            'first_name'          => $values['first_name'],
            'last_name'           => $values['last_name'],
            'gender_id'           => $values['gender_id'],
            'date_of_birth'       => $values['date_of_birth'],
            'license_expiry_date' => $values['license_expiry_date'],
            'language'            => $values['language']
        ];
        if (array_key_exists('place_of_birth', $values) && $values['place_of_birth'] != '') {
            $updateValues['place_of_birth'] = $values['place_of_birth'];
        }
        if (array_key_exists('extra_info', $values) && $values['extra_info'] != '') {
            $updateValues['extra_info'] = $values['extra_info'];
        }
        $user->userAddress()->updateOrCreate($updateValues);
    }
}
