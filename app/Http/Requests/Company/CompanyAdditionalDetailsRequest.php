<?php

namespace App\Http\Requests\Company;

use Illuminate\Validation\Rule;
use App\Rules\AddressRule;
use App\Rules\LocationRule;
use App\Rules\WorkstationRule;
use App\Rules\CompanySocialSecretaryRule;
use App\Http\Requests\ApiRequest;
use App\Rules\ValidateCompanyVatNumber;
use App\Rules\ValidateEmail;

class CompanyAdditionalDetailsRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responsible_persons'                          => ['nullable', 'array'],
            'responsible_persons.*.first_name'             => ['required', 'string'],
            'responsible_persons.*.last_name'              => ['required', 'string'],
            'responsible_persons.*.email'                  => [
                'bail',
                'required',
                'string',
                "max:255",
                new ValidateEmail(),
            ],
            'responsible_persons.*.phone_number'                  => ['required', 'string'],
            'responsible_persons.*.social_security_number' => ['required', 'string'],
            'responsible_persons.*.role'                   => 'string|required|in:' . implode(',', array_keys(config('roles_permissions.RESPONSIBLE_PERSON_ROLES'))),
            'locations'                                    => ['nullable', 'array', new LocationRule()],
            'workstations'                                 => ['nullable', 'array', new WorkstationRule()]
        ];
    }

    public function messages()
    {

        return [
            'email.required'             => t('The email field is required.'),
            'email.max'                  => t('The email field may not be greater than 255 characters.'),
            'email.regex'                => t('The email field must be a valid email address.'),

            'phone_number.required'      => t('The phone field is required.'),
            'phone_number.string'        => t('The phone field must be a string.'),
            'phone_number.max'           => t('The phone field may not be greater than 20 characters.'),
            'phone_number.regex'         => t('The phone field must be a valid phone number in the format: +XX X XXX XXXX'),
            'vat_number.unique'          => t('Company with same VAT already present.'),
            'responsible_persons.*.role' => t('Invalid role added for responsible person.'),
        ];
    }
}
