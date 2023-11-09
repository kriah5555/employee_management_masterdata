<?php

namespace App\Interfaces\User;

use App\Models\User\UserFamilyDetails;

interface UserFamilyDetailsRepositoryInterface
{

    public function getUserFamilyDetailsById(string $id);

    public function deleteUserFamilyDetails(string $id);

    public function createUserFamilyDetails(array $details);

    public function updateUserFamilyDetails(UserFamilyDetails $userFamilyDetails, array $updatedDetails);
}
