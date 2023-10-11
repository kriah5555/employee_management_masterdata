<?php

namespace App\Interfaces;

interface UserAddressRepositoryInterface
{

    public function getUserAddressById(string $id);

    public function deleteUserAddress(string $id);

    public function createUserAddress(array $details);

    public function updateUserAddress(string $id, array $updatedDetails);
}
