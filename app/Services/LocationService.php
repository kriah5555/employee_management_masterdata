<?php

namespace App\Services;

use App\Models\Location;
use App\Services\AddressService;
use App\Rules\AddressRule;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class LocationService
{
    public static function getLocationRules($for_company_creation = true) 
    {
        return [
            'location_name' => 'required|string|max:255',
            'status'        => 'required|boolean',
            'address'       => ['required', new AddressRule()],
            'company'       => [
                $for_company_creation ? 'nullable' : 'required',
                Rule::exists('companies', 'id')
            ],
        ];
    }

    public function getAllLocations()
    {
        return Location::all();
    }

    public function getActiveLocations()
    {
        return Location::where('status', '=', true)->get();
    }

    
    public function getLocationDetails($id)
    {
        return Location::findOrFail($id);
    }

    public function createNewLocation($values)
    {
        try {
            DB::beginTransaction();
            $address           = new AddressService();
            $address           = $address->createNewAddress($values['address']);
            $values['address'] = $address->id;
            $location          = Location::create($values);
            DB::commit();
            return $location ;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function updateLocation(Location $location, $values) 
    {
        try {
            DB::beginTransaction();
            $address = new AddressService();
            $address->updateAddress($location->address, $values['address']);
            unset($values['address']);
            unset($values['company']);
            $location->update($values);
            DB::commit();
            return $location;
        } catch (Exception $e) {
            DB::rollback();
            error_log($e->getMessage());
            throw $e;
        }
    }
}
