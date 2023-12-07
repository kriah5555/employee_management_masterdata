<?php

namespace App\Http\Requests\Company;

use Illuminate\Validation\Rule;
use App\Rules\AddressRule;
use App\Http\Requests\ApiRequest;
use App\Rules\ValidateCompanyVatNumber;
use App\Rules\ValidateEmail;

class CompanyRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->isMethod('post') || $this->isMethod('put')) {
            $rules = [
                'company_name'            => 'required|string|max:255',
                'sectors'                 => 'required|array',
                'sectors.*'               => [
                    'bail',
                    'integer',
                    Rule::exists('sectors', 'id'),
                ],
                'email'                   => [
                    'bail',
                    'required',
                    'string',
                    "max:255",
                    new ValidateEmail(),
                ],
                'phone'                   => [
                    'required',
                    'string',
                    'max:20',
                    // 'regex:'.config('regex.PHONE_REGEX'), # need to be added back
                ],
                'sender_number'           => 'nullable|digits_between:1,11',
                'username'                => 'nullable|string|max:50',
                'rsz_number'              => 'nullable|string',
                'interim_agencies'        => 'nullable|array',
                'interim_agencies.*'      => [
                    'bail',
                    'integer',
                    Rule::exists('interim_agencies', 'id'),
                ],
                'address'                 => ['required', new AddressRule()],
                'social_secretary_id'     => [
                    'bail',
                    'nullable',
                    Rule::exists('social_secretaries', 'id')
                ],
                'social_secretary_number' => 'nullable|string',
                'contact_email'           => [
                    'bail',
                    'nullable',
                    'string',
                    "max:255",
                    new ValidateEmail(),
                ],
                'oauth_key'               => 'nullable|string',
                'status'                  => 'boolean',
            ];
            if ($this->isMethod('post')) {
                $rules['vat_number'] = [
                    'bail',
                    'required',
                    'string',
                    new ValidateCompanyVatNumber(),
                    Rule::unique('companies')->where(function ($query) {
                        $query->where('status', true);
                    })
                ];
            } elseif ($this->isMethod('put')) {
                $rules['vat_number'] = [
                    'bail',
                    'required',
                    'string',
                    new ValidateCompanyVatNumber(),
                    Rule::unique('companies')->where(function ($query) {
                        $query->where('status', true);
                    })->ignore($this->company)
                ];
            }
        }
        return $rules;
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
