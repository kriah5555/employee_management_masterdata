<?php

namespace App\Http\Rules\Holiday; // Correct namespace declaration

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule; // Import Rule from Illuminate\Validation

class PublicHolidayRequest extends ApiRequest
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
            'name' => 'required|string|max:255',
            'date' => [
                'bail',
                'required',
                'date_format:d-m-Y',
                Rule::unique('public_holidays', 'date')->ignore($this->route('public_holiday')), # check if the date is unique or not
            ],

            'status'      => 'required|boolean',
            'companies'   => 'nullable|array',
            'companies.*' => [
                'bail',
                'integer',
                Rule::exists('companies', 'id'),
            ],
        ];
    }

    public function messages()
    {
        return [];
    }
}
