<?php

namespace App\Http\Rules\Holiday;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;

class HolidayCodesOfSocialSecretaryRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'social_secretary_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('social_secretaries', 'id'),
            ],
            'social_secretary_codes' => [
                'bail',
                'required',
                'array',
            ],
            'social_secretary_codes.*.holiday_code_id' => [
                'bail',
                'required',
                'integer',
                Rule::exists('holiday_codes', 'id'),
            ],
            'social_secretary_codes.*.social_secretary_code' => '',
        ];
    }

    public function messages()
    {
        return [];
    }
}
