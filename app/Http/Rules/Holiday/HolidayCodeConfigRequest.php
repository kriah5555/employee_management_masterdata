<?php

namespace App\Http\Rules\Holiday;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;

class HolidayCodeConfigRequest extends ApiRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules()
    {
        return [
            'holiday_code_ids' => 'bail|nullable|array',
            'holiday_code_ids.*' => [
                'bail',
                'integer',
                Rule::exists('holiday_codes', 'id')->where('status', 1),
            ]
        ];
    }

    public function messages()
    {
        return [];
    }
}
