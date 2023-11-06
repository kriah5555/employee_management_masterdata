<?php

namespace App\Interfaces\User;

use App\Models\User\UserAddress;

interface UserAddressRepositoryInterface
{

    public function getUserAddressById(string $id): UserAddress;

    public function deleteUserAddress(UserAddress $userAddress): bool;

    public function createUserAddress(array $details);

    public function updateUserAddress(UserAddress $userAddress, array $updatedDetails): bool;
}
