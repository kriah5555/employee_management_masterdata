<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Services\LocationService;

class LocationRequest extends Rule
{   
    public function rules() :array
    {
        return LocationService::getLocationRules();    
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
        ];
    }
}
