<?php

namespace App\Repositories\User;

use App\Models\User\User;
use App\Interfaces\User\UserRepositoryInterface;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;

class UserRepository implements UserRepositoryInterface
{

    public function getUserById(string $id): User
    {
        return User::findOrFail($id);
    }

    public function deleteUser(string $id)
    {
        User::destroy($id);
    }

    public function createUser(array $details): User
    {
        return User::create($details);
    }

    public function updateUser(User $user, array $updatedDetails)
    {
        if ($user->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update user basic details');
        }
    }
    public function getUserBySocialSecurityNumber(string $socialSecurityNumber)
    {
        return User::where('social_security_number', $socialSecurityNumber)->get();
    }



}
