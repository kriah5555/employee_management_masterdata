<?php

namespace App\Http\Rules;

use Illuminate\Validation\Rule;
use App\Http\Rules\ApiRequest;

class HolidayCodeCountRequest extends ApiRequest
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
            'count' => 'required|integer',
            'holiday_code_id' => 'required|exists:holiday_codes,id',
        ];
    }

    public function messages()
    {
        return [
            'count.required' => 'The Holiday count field is required',
            'count.integer' => 'The Holiday count code must be an integer.',
            
            'holiday_code_id.required' => 'The Holiday code field is required',
            'holiday_code_id.exists' => 'Invalid Holiday code',
        ];
    }
}
