<?php

namespace App\Http\Requests\Employee;

use App\Http\Requests\ApiRequest;

class ResponsiblePersonRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules =  [
            'email' => 'required|max:255|regex:' . config('regex.EMAIL_REGEX'),
            'phone' => [
                'required',
                'string',
                'max:20',
                // 'regex:' . config('regex.PHONE_REGEX'), # need to be added back
            ],
            'first_name'             => 'required|string|max:255',
            'last_name'              => 'required|string|max:255',
            'social_security_number' => 'required|' . config('constants.RSZ_NUMBER_VALIDATION'),
            'role'                   => 'required|in:' . config('roles_permissions.CUSTOMER_ADMIN') . ',' . config('roles_permissions.MANAGER'),
        ];
        return $rules;
    }
}
