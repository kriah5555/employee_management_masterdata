<?php

namespace App\Services;

use App\Models\Locations;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;

class LocationService
{
    public static function getLocationRules($for_company_creation = true) 
    {
        return [
            'location_name' => 'required|string|max:255',
            'status'        => 'required|boolean',
            'address'       => ['required', new AddressRule()],
            'company'       => [
                Rule::exists('companies', 'id')
            ],
        ];
    }

    
    public function getLocationDetails($id)
    {
        return Locations::findOrFail($id);
    }

    public function createNewLocations($values)
    {
        try {
            $address  = new AddressService();
            $address  = $address->createNewAddress($values['address']);
            $values['address'] = $address->id;
            $location = Locations::create($values);
            return $location ;
        } catch (Exception $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
}
