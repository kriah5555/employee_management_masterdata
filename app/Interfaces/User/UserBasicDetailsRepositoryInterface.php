<?php

namespace App\Interfaces;

interface UserBasicDetailsRepositoryInterface
{

    public function getUserBasicDetailsById(string $id);

    public function deleteUserBasicDetails(string $id);

    public function createUserBasicDetails(array $details);

    public function updateUserBasicDetails(string $id, array $updatedDetails);
}
