<?php

namespace App\Repositories\User;

use App\Models\User\UserBankAccount;
use App\Exceptions\ModelDeleteFailedException;
use App\Exceptions\ModelUpdateFailedException;
use App\Interfaces\User\UserBankAccountRepositoryInterface;


class UserBankAccountRepository implements UserBankAccountRepositoryInterface
{

    public function getUserBankAccountById(string $id): UserBankAccount
    {
        return UserBankAccount::findOrFail($id);
    }

    public function createUserBankAccount(array $details): UserBankAccount
    {
        return UserBankAccount::create($details);
    }

    public function deleteUserBankAccount(UserBankAccount $userBankAccount): bool
    {
        if ($userBankAccount->delete()) {
            return true;
        } else {
            throw new ModelDeleteFailedException('Failed to delete user bank account');
        }
    }

    public function updateUserBankAccount(UserBankAccount $userBankAccount, array $updatedDetails): bool
    {
        if ($userBankAccount->update($updatedDetails)) {

            return true;
        } else {
            throw new ModelUpdateFailedException('Failed to update user bank account');
        }
    }
}
