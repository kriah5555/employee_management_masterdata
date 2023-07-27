<?php

namespace App\Http\Rules;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

class ApiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
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
