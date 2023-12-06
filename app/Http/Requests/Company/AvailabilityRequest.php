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
            'company_ids'   => [
                'required',
                'array',
            ],
            'company_ids.*' => [
                'bail',
                'integer',
                Rule::exists('master.company_users', 'company_id'),
            ],
            'type'          => 'required|between:0,1|bail',
            'remark'        => 'bail|string',
            'dates'         => [
                'required',
                'array',
                'bail',
            ],
            'dates.*'       => 'date_format:' . config('constants.DEFAULT_DATE_FORMAT')
        ];
    }

    public function messages()
    {
        return [
            'company_ids.required' => 'Please select the company.',
            'type.required'        => 'Please select the availability option.',
            'dates.required'       => 'Date is required.',
            'dates.array'          => 'Dates must be in an array.',
            'dates.date'           => 'The date should be in the day-month-year format (e.g., 20-12-2023).',
        ];
    }
}
