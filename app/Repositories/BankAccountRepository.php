<?php

namespace App\Repositories;

use App\Interfaces\BankAccountRepositoryInterface;
use App\Models\BankAccount;

class BankAccountRepository implements BankAccountRepositoryInterface
{
    public function getAllBankAccounts()
    {
        return BankAccount::all();
    }

    public function getBankAccountById(string $bankAccountId): BankAccount
    {
        return BankAccount::findOrFail($bankAccountId);
    }

    public function deleteBankAccount(string $bankAccountId)
    {
        BankAccount::destroy($bankAccountId);
    }

    public function createBankAccount(array $bankAccountDetails): BankAccount
    {
        return BankAccount::create($bankAccountDetails);
    }

    public function updateBankAccount(string $bankAccountId, array $newDetails)
    {
        return BankAccount::whereId($bankAccountId)->update($newDetails);
    }
}
