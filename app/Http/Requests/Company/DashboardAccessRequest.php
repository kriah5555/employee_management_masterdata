<?php

namespace App\Http\Requests\Company;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class DashboardAccessRequest extends ApiRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'unique_key'   => 'required|string',
            'type'         => [
                'required',
                'integer',
                Rule::in(array_keys(config('constants.DASHBOARD_ACCESS_OPTIONS')))
            ],
            'location_id'  => [
                'required',
                'integer',
                Rule::exists('locations','id'),
            ],
            'status'       => 'boolean',
        ];
    }

    public function messages()
    {
        return [
            'unique_key.required' => 'Unique_key is required.',
            'type.integer'       => 'Type must be an integer.',
        ];
    }
}
