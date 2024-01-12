<?php

namespace App\Http\Requests\Employee;

// use App\Http\Rules\ApiRequest;
use App\Rules\User\GenderRule;
use Illuminate\Validation\Rule;
use App\Http\Requests\ApiRequest;

use Illuminate\Http\JsonResponse;
use App\Rules\DuplicateSocialSecurityNumberRule;
use App\Rules\ValidateLengthIgnoringSymbolsRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateEmployeeWebRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */

     public function rules(): array
     {
         $path = $this->getPathInfo(); // Get the path of the current URL

           if(str_contains($path, 'update-employee-personal-details')) {
             $rules = [
                'user_id'                => 'required|integer',
                'first_name'             => 'required|string|max:255',
                'last_name'              => 'required|string|max:255',
                'gender_id'              => [
                    'bail',
                    'required',
                    'integer',
                    new GenderRule(),
                ],
                'date_of_birth'          => 'required|date|date_format:d-m-Y|before:today',
                'nationality'            => 'required|string|max:50',
                'phone_number'           => 'nullable|string|max:255',
                'email'                  => 'required|email',
                'social_security_number' => [
                    'required',
                    'string',
                    new ValidateLengthIgnoringSymbolsRule(11, 11, [',', '.', '-']),
                    // new DuplicateSocialSecurityNumberRule(new EmployeeProfileRepository)
                ],
                'account_number'         => 'nullable|string|max:255',

             ];
         }
         else if(str_contains($path, 'update-employee-address-details')) {
             $rules = [

                'user_id'                => 'required|integer',
                'street_house_no'        => 'required|string|max:255',
                'postal_code'            => 'required|string|max:50',
                'city'                   => 'required|string|max:50',
                'country'                => 'required|string|max:50',

             ];
         }

         return $rules;
     }

    public function messages()
    {
        return [
            'user_id.required'    => 'User id is required',
            'user_id.exists'      => 'User not exists',
            'first_name.required' => 'First name is required',
            'first_name.string'   => 'First name details are wrong',
            'last_name.required'  => 'Last name is required',
            // 'rsz_number.exists' => 'RSZ number already exists, please copy the employee details'
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
