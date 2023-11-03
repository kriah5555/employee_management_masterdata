<?php

namespace App\Repositories\User;

use App\Interfaces\User\UserRepositoryInterface;
use App\Models\User\User;

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

    public function updateUser(string $id, array $updatedDetails)
    {
        return User::whereId($id)->update($updatedDetails);
    }
    public function getUserBySocialSecurityNumber(string $socialSecurityNumber)
    {
        return User::where('social_security_number', $socialSecurityNumber)->get();
    }
}
