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
            'employee_id' => ['required' , 'int',  Rule::exists('employee_profiles', 'id'),],
            'type' => 'required|between:0,1|bail',
            'remark' => 'required|string|bail',
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
            'employee_id.required' => 'Employee is required.',
            'company_id.required' => 'Please select the company.',
            'type.required' => 'Please select the availability option.',
            'remark.required' => 'The remark field is required.',
            'dates.required' => 'Date is required.',
            'dates.array' => 'Dates must be in an array.',
            'dates.regex' => 'The date should be in the day-month-year format (e.g., 20-12-2023).',
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
