<?php

namespace App\Interfaces\User;

use App\Models\User\UserBasicDetails;

interface UserBasicDetailsRepositoryInterface
{

    public function getUserBasicDetailsById(string $id);

    public function deleteUserBasicDetails(string $id);

    public function createUserBasicDetails(array $details);

    public function updateUserBasicDetails(UserBasicDetails $userBasicDetails, array $updatedDetails);
}
