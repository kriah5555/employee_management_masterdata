<?php

namespace App\Http\Requests\Employee;

// use App\Http\Rules\ApiRequest;
use App\Rules\User\GenderRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;

use Illuminate\Http\JsonResponse;
use App\Rules\DuplicateSocialSecurityNumberRule;
use App\Rules\ValidateLengthIgnoringSymbolsRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {

        return [
            'user_id'                => ['required'],
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'gender_id'              => [
                'bail',
                'required',
                'integer',
                new GenderRule(),
            ],
            'date_of_birth'          => 'required|date|date_format:d-m-Y|before:today',
            'street_house_no'        => 'required|string|max:255',
            'postal_code'            => 'required|string|max:50',
            'city'                   => 'required|string|max:50',
            'country'                => 'required|string|max:50',
            'nationality'            => 'required|string|max:50',
            'phone_number'           => 'nullable|string|max:255',
            'email'                  => 'required|email',
            'social_security_number' => [
                'required',
                'string',
                new ValidateLengthIgnoringSymbolsRule(11, 11, [',', '.', '-']),
                // new DuplicateSocialSecurityNumberRule(new EmployeeProfileRepository)
            ],
            'account_number'         => 'nullable|string|max:255',


        ];

    }
    public function messages()
    {
        return [
            'user_id.required'    => 'User id is required',
            'user_id.exists'      => 'User not exists',
            'first_name.required' => 'First name is required',
            'first_name.string'   => 'First name details are wrong',
            'last_name.required'  => 'Last name is required',
            // 'rsz_number.exists' => 'RSZ number already exists, please copy the employee details'
        ];
    }

}
