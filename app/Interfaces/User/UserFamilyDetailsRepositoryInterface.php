<?php

namespace App\Interfaces;

interface UserFamilyDetailsRepositoryInterface
{

    public function getUserFamilyDetailsById(string $id);

    public function deleteUserFamilyDetails(string $id);

    public function createUserFamilyDetails(array $details);

    public function updateUserFamilyDetails(string $id, array $updatedDetails);
}
