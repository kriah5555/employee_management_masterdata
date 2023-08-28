<?php

namespace App\Http\Rules;

use App\Services\LocationService;

class LocationRequest extends ApiRequest
{
    public function rules() :array
    {
        $location_rules = LocationService::getLocationRules(false);
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            unset($location_rules['company']);
        }
        return $location_rules;
    }

    public function messages()
    {
        return [
            'loaction_name.required' => 'The location name field is required.',
            'loaction_name.string'   => 'The location name must be a string.',
            'loaction_name.max'      => 'The location name may not be greater than 255',

            'status.boolean' => 'The address status field must be a boolean value.',

            'company.required' => 'The company field is required.',
            'company.exists'   => 'The selected company does not exist.',

            'address.required' => 'The address field is required.',
        ];
    }
}
