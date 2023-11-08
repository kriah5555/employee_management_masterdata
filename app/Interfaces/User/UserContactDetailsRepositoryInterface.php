<?php

namespace App\Interfaces\User;

use App\Models\User\UserContactDetails;

interface UserContactDetailsRepositoryInterface
{

    public function getUserContactDetailsById(string $id);

    public function deleteUserContactDetails(string $id);

    public function createUserContactDetails(array $details);

    public function updateUserContactDetails(UserContactDetails $userContactDetails, array $updatedDetails);
}
