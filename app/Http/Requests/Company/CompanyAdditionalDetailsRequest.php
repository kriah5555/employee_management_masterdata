<?php

namespace App\Http\Requests\Company;

use Illuminate\Validation\Rule;
use App\Rules\AddressRule;
use App\Rules\LocationRule;
use App\Rules\WorkstationRule;
use App\Rules\CompanySocialSecretaryRule;
use App\Http\Requests\ApiRequest;
use App\Rules\ValidateCompanyVatNumber;

class CompanyAdditionalDetailsRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'locations'    => ['nullable', 'array', new LocationRule()],
            'workstations' => ['nullable', 'array', new WorkstationRule()]
        ];
    }

    public function messages()
    {

        return [
            'email.required'    => t('The email field is required.'),
            'email.max'         => t('The email field may not be greater than 255 characters.'),
            'email.regex'       => t('The email field must be a valid email address.'),

            'phone.required'    => t('The phone field is required.'),
            'phone.string'      => t('The phone field must be a string.'),
            'phone.max'         => t('The phone field may not be greater than 20 characters.'),
            'phone.regex'       => t('The phone field must be a valid phone number in the format: +XX X XXX XXXX'),
            'vat_number.unique' => t('Company with same VAT already present.'),
        ];
    }
}
