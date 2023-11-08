<?php

namespace App\Services\Company;

use App\Models\Company\CompanyAddress;

class AddressService
{
    public function getAddressDetails($id)
    {
        return CompanyAddress::findOrFail($id);
    }

    public function createNewAddress($values)
    {
        try {
            return CompanyAddress::create($values);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateAddress($address_id, $values)
    {
        try {
            $address = CompanyAddress::where('id', '=', $address_id)->update($values);
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
