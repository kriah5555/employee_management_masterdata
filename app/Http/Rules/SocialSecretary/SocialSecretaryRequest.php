<?php

namespace App\Http\Rules\SocialSecretary;

use App\Http\Rules\ApiRequest;

class SocialSecretaryRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'   => 'required|string|max:255',
            'status' => 'required|boolean',
        ];
    }

    public function messages()
    {
        return [];
    }
}
