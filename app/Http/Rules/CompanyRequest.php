<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use App\Rules\AddressRule;
use App\Rules\LocationRule;
use App\Rules\WorkstationRule;

class CompanyRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name'            => 'required|string|max:255',
            'status'                  => 'required|boolean',
            'sectors'                 => 'required|array',
            'address'                 => ['required', new AddressRule()],
            'locations'               => ['nullable', 'array', new LocationRule()],
            'workstations'            => ['nullable', 'array', new WorkstationRule()],
            'sectors.*'               => [
                Rule::exists('sectors', 'id'),
            ],

            'employer_id'             => 'nullable|digits_between:1,11',
            'sender_number'           => 'nullable|digits_between:1,11',
            // 'joint_commission_number' => 'nullable|digits_between:1,11',
            'rsz_number'              => 'nullable|digits_between:1,11',
            'social_secretary_number' => 'nullable|digits_between:1,11',
            'username'                => 'nullable|string|max:50',
            'email'                   => 'required|max:255|regex:/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
            'phone'                   => [
                'required',
                'string',
                'max:20',
                //'regex:/^(\+[0-9]{1,4}\s[0-9]{1,3}\s[0-9]{1,3}\s[0-9\s]+)$/',
            ],
        ];
    }

    public function messages()
    {

        return [
        ];
    }
}
