<?php

namespace App\Http\Rules\Employee;

use App\Http\Rules\ApiRequest;
use App\Rules\SocialSecurityNumberRule;
use App\Rules\ValidateLengthIgnoringSymbols;
use App\Rules\User\GenderRule;

class UpdateEmployeePersonalDetailsRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'date_of_birth'          => 'required|date',
            'gender_id'              => [
                'bail',
                'required',
                'integer',
                new GenderRule(),
            ],
            'email'                  => 'required|email',
            'phone_number'           => 'required|string|max:20',
            'social_security_number' => [
                'required',
                'string',
                new ValidateLengthIgnoringSymbols(11, 11, [',', '.', '-']),
                // new SocialSecurityNumberRule(new EmployeeProfileRepository)
            ],
            'place_of_birth'         => 'string|max:255',
            'license_expiry_date'    => 'nullable|date',
            'bank_account_number'    => 'nullable|string|max:255',
            'street_house_no'        => 'required|string|max:255',
            'postal_code'            => 'required|string|max:50',
            'city'                   => 'required|string|max:50',
            'country'                => 'required|string|max:50',
            'nationality'            => 'required|string|max:50',
            'latitude'               => 'nullable|numeric',
            'longitude'              => 'nullable|numeric',
        ];

    }
    public function messages()
    {
        return [];
    }
}
