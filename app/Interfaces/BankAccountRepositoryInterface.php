<?php

namespace App\Interfaces;

interface BankAccountRepositoryInterface
{
    public function getAllBankAccounts();

    public function getBankAccountById(string $bankAccountId);

    public function deleteBankAccount(string $bankAccountId);

    public function createBankAccount(array $bankAccountDetails);

    public function updateBankAccount(string $bankAccountId, array $newDetails);
}
