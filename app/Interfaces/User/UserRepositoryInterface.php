<?php

namespace App\Interfaces\User;

use App\Models\User\User;

interface UserRepositoryInterface
{

    public function getUserById(string $id);

    public function deleteUser(string $id);

    public function createUser(array $details);

    public function updateUser(User $user, array $updatedDetails);
}
