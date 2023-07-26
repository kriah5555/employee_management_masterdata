<?php

namespace App\Http\Rules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class SectorRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'paritair_committee' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'employee_types' => 'nullable|array',
            'employee_types.*' => [
                Rule::exists('employee_types', 'id'),
            ],
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Name is required.',
            'name.string' => 'Name must be a string.',
            'name.max' => 'Name cannot be greater than 255 characters.',
            'paritair_committee.required' => 'Paritair committee is required.',
            'paritair_committee.string' => 'Paritair committee must be a string.',
            'paritair_committee.max' => 'Paritair committee cannot be greater than 255 characters.',
            'description.string' => 'Description must be a string.',
            'description.max' => 'Description cannot be greater than 255 characters.',
            'status.boolean' => 'Status must be a boolean value.',      
            'category.required' => 'Category is required.',
            'category.max' => 'Category cannot be greater than 255 characters.',
            'category.custom_integer_rule' => 'Category must be an integer.', // Add this message
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'status'  => 422, # validation error status
                'message' => 'Validation error',
                'data'    => [
                    'errors' => $validator->errors()
                ],
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY)
        );
    }
}
