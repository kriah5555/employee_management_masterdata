<?php

namespace App\Http\Rules\Employee;

use App\Http\Rules\ApiRequest;
use App\Rules\AddressRule;
use App\Rules\SocialSecurityNumberRule;
use App\Repositories\EmployeeProfileRepository;

class EmployeeProfileRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        $allowedLanguageValues = config('app.available_locales');
        return [
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'date_of_birth'          => 'required|date',
            'gender'                 => 'string|max:255',
            'email'                  => 'required|email',
            'phone_number'           => 'required|string|max:20',
            'social_security_number' => [
                'required',
                'string',
                'min:11',
                'max:11',
                new SocialSecurityNumberRule(new EmployeeProfileRepository)
            ],
            'place_of_birth'         => 'string|max:255',
            'date_of_joining'        => 'required|date',
            'date_of_leaving'        => 'date',
            'language'               => ['required', 'string', 'in:' . implode(',', $allowedLanguageValues)],
            'marital_status'         => 'string|max:255',
            'dependent_spouse'       => 'string|max:255',
            'bank_account_number'    => 'string|max:255',
            'address'                => ['required', new AddressRule()],
        ];

    }
    public function messages()
    {
        return [];
    }
}
