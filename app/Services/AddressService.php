<?php

namespace App\Services;

use App\Models\Address;

class AddressService
{
    public function getAddressDetails($id)
    {
        return Address::findOrFail($id);
    }

    public function createNewAddress($values)
    {
        try {
            return Address::create($values);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateAddress(Address $address, $values)
    {
        try {
            $address->update($values);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
