<?php

namespace App\Repositories\User;

use App\Interfaces\User\UserAddressRepositoryInterface;
use App\Models\User\UserAddress;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;

class UserAddressRepository implements UserAddressRepositoryInterface
{

    public function getUserAddressById(string $id): UserAddress
    {
        return UserAddress::findOrFail($id);
    }

    public function createUserAddress(array $details): UserAddress
    {
        return UserAddress::create($details);
    }

    public function deleteUserAddress(UserAddress $userAddress): bool
    {
        if ($userAddress->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete user address');
        }
    }

    public function updateUserAddress(UserAddress $userAddress, array $updatedDetails): bool
    {
        if ($userAddress->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update user address');
        }
    }
}
