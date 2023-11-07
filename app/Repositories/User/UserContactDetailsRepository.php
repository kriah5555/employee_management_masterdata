<?php

namespace App\Repositories\User;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\User\UserContactDetailsRepositoryInterface;
use App\Models\User\UserContactDetails;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserContactDetailsRepository implements UserContactDetailsRepositoryInterface
{
    public function getUserContactDetailsById(string $id, array $relations = []): Collection|Builder|UserContactDetails
    {
        return UserContactDetails::with($relations)->findOrFail($id);
    }
    public function deleteUserContactDetails($userContactDetails): bool
    {
        if ($userContactDetails->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete user contact details');
        }
    }


    public function createUserContactDetails(array $details): UserContactDetails
    {
        return UserContactDetails::create($details);
    }

    public function updateUserContactDetails(UserContactDetails $userContactDetails, array $updatedDetails): bool
    {
        if ($userContactDetails->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update user contact details');
        }
    }
}
