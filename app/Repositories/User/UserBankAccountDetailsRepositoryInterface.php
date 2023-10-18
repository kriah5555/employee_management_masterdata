<?php

namespace App\Interfaces;

interface UserBankAccountDetailsRepositoryInterface
{

    public function getUserBankAccountDetailsById(string $id);

    public function deleteUserBankAccountDetails(string $id);

    public function createUserBankAccountDetails(array $details);

    public function updateUserBankAccountDetails(string $id, array $updatedDetails);
}
