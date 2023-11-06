<?php

namespace App\Interfaces\User;

use App\Models\User\UserBankAccount;

interface UserBankAccountRepositoryInterface
{

    public function getUserBankAccountById(string $id): UserBankAccount;

    public function deleteUserBankAccount(UserBankAccount $userBankAccount);

    public function createUserBankAccount(array $details): UserBankAccount;

    public function updateUserBankAccount(UserBankAccount $userBankAccount, array $updatedDetails);
}
