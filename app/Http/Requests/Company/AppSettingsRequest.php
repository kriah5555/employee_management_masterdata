<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\ApiRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

class AppSettingsRequest extends ApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */

    public function rules(): array
    {
        return [

            'user_id'  => [
                'required' ,
                'int',
                // Rule::exists('users', 'id'),
            ],
            'type' => [
                    'required',
                    'bail',
                    Rule::in(array_keys(config('constants.APP_SETTINGS_OPTIONS')))
            ],
            'app_setting_options_id' => [
                'required',
                'bail',
                 Rule::exists('app_settings','id')
            ]
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
