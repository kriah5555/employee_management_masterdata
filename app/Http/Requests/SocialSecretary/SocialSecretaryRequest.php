<?php

namespace App\Http\Requests\SocialSecretary;

use App\Http\Requests\ApiRequest;

class SocialSecretaryRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'daily_registration' => 'required|boolean',
            'status'             => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [];
    }
}
