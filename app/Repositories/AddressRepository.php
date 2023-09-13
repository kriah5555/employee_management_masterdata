<?php

namespace App\Repositories;

use App\Interfaces\AddressRepositoryInterface;
use App\Models\Address;

class AddressRepository implements AddressRepositoryInterface
{
    public function getAllAddresses()
    {
        return Address::all();
    }

    public function getAddressById(string $AddressId): Address
    {
        return Address::findOrFail($AddressId);
    }

    public function deleteAddress(string $AddressId)
    {
        Address::destroy($AddressId);
    }

    public function createAddress(array $AddressDetails): Address
    {
        return Address::create($AddressDetails);
    }

    public function updateAddress(string $AddressId, array $newDetails)
    {
        return Address::whereId($AddressId)->update($newDetails);
    }
}
