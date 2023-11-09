<?php

namespace App\Repositories\User;

use App\Interfaces\User\UserFamilyDetailsRepositoryInterface;
use App\Models\User\UserFamilyDetails;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserFamilyDetailsRepository implements UserFamilyDetailsRepositoryInterface
{
    public function getUserFamilyDetailsById(string $id, array $relations = []): Collection|Builder|UserFamilyDetails
    {
        return UserFamilyDetails::with($relations)->findOrFail($id);
    }
    public function deleteUserFamilyDetails($userFamilyDetails): bool
    {
        if ($userFamilyDetails->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete user family details');
        }
    }


    public function createUserFamilyDetails(array $details): UserFamilyDetails
    {
        return UserFamilyDetails::create($details);
    }

    public function updateUserFamilyDetails(UserFamilyDetails $userFamilyDetails, array $updatedDetails): bool
    {
        if ($userFamilyDetails->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update user contact details');
        }
    }
}
