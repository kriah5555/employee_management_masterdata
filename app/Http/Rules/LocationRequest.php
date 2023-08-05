<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LocationRequest extends Rule
{   
    public function rules() :array
    {
        return [
            'loaction_name'  => 'required|string|max:255',
            'status'         => 'boolean',
            'company'        => [
                'required',
                Rule::exists('company', 'id'),
            ],            
        ];
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
