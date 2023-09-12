<?php

namespace App\Interfaces;

interface AddressRepositoryInterface
{
    public function getAllAddresses();

    public function getAddressById(string $addressId);

    public function deleteAddress(string $addressId);

    public function createAddress(array $addressDetails);

    public function updateAddress(string $addressId, array $newDetails);
}
