<?php

namespace App\Http\Rules\SocialSecretary;

use Illuminate\Validation\Rule;
use App\Rules\AddressRule;
use App\Rules\LocationRule;
use App\Rules\WorkstationRule;
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
