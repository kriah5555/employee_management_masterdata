<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\ApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AvailabilityRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */

    public function rules(): array
    {
        return [
            'user_id'     => ['required' , 'int',  Rule::exists('userdb.users', 'id'),],
            'type'        => 'required|between:0,1|bail',
            'remark'      => 'required|string|bail',
            'company_ids' => ['bail', 'required', 'array', Rule::exists('master.companies', 'id')],
            'dates' => [
                'required',
                'array',
                'bail',
            ],
            'dates.*' => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT'),
        ];
    }

    public function messages()
    {
        return [
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $errors = $validator->errors()->all();

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => implode(' ', $errors),
        ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
    }
}
