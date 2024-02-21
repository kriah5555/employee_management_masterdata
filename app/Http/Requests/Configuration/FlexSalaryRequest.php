<?php

namespace App\Http\Requests\Configuration;

use App\Http\Requests\ApiRequest;
use App\Rules\BelgiumCurrencyFormatRule;

class FlexSalaryRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'value' => ['required', new BelgiumCurrencyFormatRule],
        ];
    }
}
