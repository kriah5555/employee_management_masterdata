<?php

namespace App\Services\User;

use App\Repositories\User\UserRepository;


class UserService
{

    protected $userRepository;


    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserBySocialSecurityNumber($socialSecurityNumber)
    {
        return $this->userRepository->getUserBySocialSecurityNumber($socialSecurityNumber);
    }
}
