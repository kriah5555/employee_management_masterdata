<?php

namespace App\Services;

use App\Models\Address;
use Exception;

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

    public function updateAddress($address_id, $values)
    {
        try {
            Address::where('id', '=', $address_id)->update($values);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
