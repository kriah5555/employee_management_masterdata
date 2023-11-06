<?php

namespace App\Repositories\User;

use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\User\UserBasicDetailsRepositoryInterface;
use App\Models\User\UserBasicDetails;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class UserBasicDetailsRepository implements UserBasicDetailsRepositoryInterface
{
    public function getUserBasicDetailsById(string $id, array $relations = []): Collection|Builder|UserBasicDetails
    {
        return UserBasicDetails::with($relations)->findOrFail($id);
    }
    public function deleteUserBasicDetails($userBasicDetails): bool
    {
        if ($userBasicDetails->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete user basic details');
        }
    }


    public function createUserBasicDetails(array $details): UserBasicDetails
    {
        return UserBasicDetails::create($details);
    }

    public function updateUserBasicDetails(UserBasicDetails $userBasicDetails, array $updatedDetails): bool
    {
        if ($userBasicDetails->update($updatedDetails)) {
            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update user basic details');
        }
    }
}
