<?php

namespace App\Http\Rules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class CompanyRules extends FormRequest
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
            'company_name'  => 'required|string|max:255',
            'street'        => 'required|string|max:255',
            'postal_code'   => 'integer|max:255',
            'city'          => 'required|string|max:255',
            'country'       => 'required|string|max:255',
            'status'        => 'required|boolean',
            'logo'          => 'required',
            'sectors'       => 'required|array',
            'sectors.*'     => [
                Rule::exists('sectors', 'id'),
            ],
        ];
    }
    public function messages()
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.string'   => 'Company name must be a string.',
            'company_name.max'      => 'Company name cannot be greater than 255 characters.',

            'street.required' => 'Street is required.',
            'street.string'   => 'Street must be a string.',
            'street.max'      => 'Street cannot be greater than 255 characters.',

            'postal_code.integer' => 'Postal code must be an integer.',
            'postal_code.max'     => 'Postal code cannot be greater than 255.',

            'city.required' => 'City is required.',
            'city.string'   => 'City must be a string.',
            'city.max'      => 'City cannot be greater than 255 characters.',

            'country.required' => 'Country is required.',
            'country.string'   => 'Country must be a string.',
            'country.max'      => 'Country cannot be greater than 255 characters.',

            'status.required' => 'Status is required.',
            'status.boolean'  => 'Status must be a boolean value.',

            'logo.required' => 'Logo is required.',
            'logo.string'   => 'Logo must be a string.',
            'logo.max'      => 'Logo cannot be greater than 255 characters.',

            'sector_id.required' => 'Sector ID is required.',
            'sector_id.array' => 'Sector ID must be an array.',
            'sector_id.*.exists' => 'One or more selected sector IDs are invalid.',
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
