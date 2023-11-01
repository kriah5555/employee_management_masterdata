<?php

namespace App\Http\Rules\Interim;

use Illuminate\Validation\Rule;
use App\Rules\AddressRule;
use App\Http\Rules\ApiRequest;

class InterimAgencyRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                      => 'required|string|max:255',
            'email'                     => 'required|string|max:255',
            'companies'                 => 'nullable|array',
            'companies.*'               => [
                'bail',
                'integer',
                Rule::exists('companies', 'id'),
            ],
            'employer_id'               => 'nullable|digits_between:1,11',
            'sender_number'             => 'nullable|digits_between:1,11',
            'username'                  => 'nullable|string|max:50',
            'joint_commissioner_number' => 'nullable',
            'rsz_number'                => 'nullable|digits_between:1,11',
            'status'                    => 'required|boolean',
        ];
    }

    public function messages()
    {

        return [];
    }
}
