<?php

namespace App\Repositories;

use App\Interfaces\UserFamilyDetailsRepositoryInterface;
use App\Models\UserProfile;

class UserFamilyDetailsRepository implements UserFamilyDetailsRepositoryInterface
{

    public function getUserFamilyDetailsById(string $id): User
    {
        return User::findOrFail($id);
    }

    public function deleteUserFamilyDetails(string $id)
    {
        User::destroy($id);
    }

    public function createUserFamilyDetails(array $details): User
    {
        return User::create($details);
    }

    public function updateUserFamilyDetails(string $id, array $updatedDetails)
    {
        return User::whereId($id)->update($updatedDetails);
    }
}
