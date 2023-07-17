<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\JsonResponse;

class EmployeeTypeRequest extends FormRequest
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
    public function rules(): array
    {
        return [
            'id' => 'numeric',
            'name' => 'required|string|max:255',
            'key' => 'string|max:255',
            'description' => 'string|max:255',
            'status' => 'boolean'
        ];

    }
    public function messages()
    {
        return [
            'name.required' => 'Employee type name is required.',
            'name.string' => 'Employee type must be a string.',
            'name.max' => 'Employee type cannot be greater than 255 characters.',
            'key.required' => 'Key is required.',
            'key.string' => 'Key must be a string.',
            'key.max' => 'Key cannot be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description cannot be greater than 255 characters.',
            'status.boolean' => 'Status must be a boolean value.'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
