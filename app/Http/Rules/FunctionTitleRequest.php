<?php

namespace App\Http\Rules;

use App\Http\Rules\ApiRequest;
use Illuminate\Validation\Rule;

class FunctionTitleRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:255',
            'function_code'        => 'required|string|max:255',
            'description'          => 'nullable|string',
            'status'               => 'required|boolean',
            'function_category_id' => [
                'required',
                Rule::exists('function_category', 'id'),
            ],
        ];

    }
    public function messages()
    {
        return [
            'name.required'      => 'Name is required.',
            'name.string'        => 'Name must be a string.',
            'name.max'           => 'Name cannot be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max'    => 'Description cannot be greater than 255 characters.',
            'status.boolean'     => 'Status must be a boolean value.'
        ];
    }
}