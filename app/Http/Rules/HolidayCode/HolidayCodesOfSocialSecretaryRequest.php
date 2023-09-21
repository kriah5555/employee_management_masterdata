<?php

namespace App\Http\Rules\HolidayCode;

use App\Http\Rules\ApiRequest;

class HolidayCodesOfSocialSecretaryRequest extends ApiRequest
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
