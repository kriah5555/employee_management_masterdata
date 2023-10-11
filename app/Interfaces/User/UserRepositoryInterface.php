<?php

namespace App\Interfaces;

interface UserRepositoryInterface
{

    public function getUserById(string $id);

    public function deleteUser(string $id);

    public function createUser(array $details);

    public function updateUser(string $id, array $updatedDetails);
}
