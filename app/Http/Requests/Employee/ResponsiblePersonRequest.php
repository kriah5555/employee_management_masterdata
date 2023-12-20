<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;
use App\Rules\ValidateLengthIgnoringSymbolsRule;

class ResponsiblePersonRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'email'                  => 'required|max:255|regex:' . config('regex.EMAIL_REGEX'),
            'phone_number'           => [
                'required',
                'string',
                'max:20',
                // 'regex:' . config('regex.PHONE_REGEX'), # need to be added back
            ],
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'social_security_number' => 'required', 'string', new ValidateLengthIgnoringSymbolsRule(11, 11, [',', '.', '-']),
            'role'                   => 'required|in:' . implode(',', array_keys(config('roles_permissions.RESPONSIBLE_PERSON_ROLES'))),
            'date_of_birth'          => 'required|date_format:d-m-Y',
        ];
        return $rules;
    }
}
